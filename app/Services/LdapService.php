<?php

namespace App\Services;

class LdapService
{
    protected $connection;
    protected $baseDn;

    public function __construct()
    {
        $dnString = config('services.ldap.base_dn');
        // Mendukung multiple Base DN dipisah titik koma (;)
        $this->baseDn = array_filter(array_map('trim', explode(';', $dnString)));
    }

    /**
     * Connect to LDAP server
     */
    protected function connect()
    {
        $host = config('services.ldap.host', '127.0.0.1');
        $port = config('services.ldap.port', 389);
        $protocol = ($port == 636) ? 'ldaps://' : 'ldap://';
        $uri = $protocol . $host . ':' . $port;

        // Fix Deprecation: use URL instead of host/port arguments
        $this->connection = \ldap_connect($uri);

        if (config('services.ldap.ignore_cert', false)) {
            putenv('LDAPTLS_REQCERT=never');
        }

        \ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        \ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);

        return $this->connection;
    }

    /**
     * Authenticate a user against LDAP
     */
    public function authenticate($username, $password)
    {
        $conn = $this->connect();
        if (!$conn)
            return false;

        // Active Directory usually requires user@domain or DN
        // We attempt to find the user's DN first using the admin account
        $userDn = $this->findUserDn($username);

        if ($userDn) {
            return @\ldap_bind($conn, $userDn, $password);
        }

        return false;
    }

    /**
     * Find User DN by username using Admin credentials
     */
    protected function findUserDn($username)
    {
        $conn = $this->connect();
        $adminUser = config('services.ldap.admin_user');
        $adminPass = config('services.ldap.admin_pass');

        if (@\ldap_bind($conn, $adminUser, $adminPass)) {
            $filter = "(samaccountname=$username)";
            try {
                $search = @\ldap_search($conn, $this->baseDn, $filter, ['dn']);

                if ($search) {
                    $entries = @\ldap_get_entries($conn, $search);
                    if ($entries !== false && $entries['count'] > 0) {
                        return $entries[0]['dn'];
                    }
                }
            } catch (\Throwable $e) {
                // Mencegah aplikasi crash jika Base DN tidak valid / No such object
            }
        }

        return null;
    }

    /**
     * Get all users for synchronization
     */
    public function getAllUsers()
    {
        $conn = $this->connect();
        $adminUser = config('services.ldap.admin_user');
        $adminPass = config('services.ldap.admin_pass');

        if (@\ldap_bind($conn, $adminUser, $adminPass)) {
            \Log::info("LDAP Bind successful for admin user: " . $adminUser);
            
            $users = [];
            $seenGuids = [];
            
            foreach ($this->baseDn as $dn) {
                \Log::info("Searching LDAP in Base DN: " . $dn);
                
                $pageSize = 1000;
                $cookie = '';
                
                $filter = "(objectClass=user)";
                $attributes = ['samaccountname', 'cn', 'name', 'mail', 'objectguid', 'dn', 'employeeid', 'department', 'title', 'telephonenumber', 'extensionattribute1', 'description'];

                do {
                    try {
                        // Set paging control
                        @\ldap_control_paged_result($conn, $pageSize, true, $cookie);

                        $search = @\ldap_search($conn, $dn, $filter, $attributes);

                        if ($search) {
                            $entries = @\ldap_get_entries($conn, $search);
                            if ($entries !== false) {
                                for ($i = 0; $i < $entries['count']; $i++) {
                                    $entry = $entries[$i];
                                    
                                    // GUID binary
                                    $guid = isset($entry['objectguid'][0]) ? bin2hex($entry['objectguid'][0]) : null;
                                    
                                    // Skip jika sudah diproses
                                    if ($guid && in_array($guid, $seenGuids)) continue;
                                    if ($guid) $seenGuids[] = $guid;

                                    // Fallback username
                                    $username = $entry['samaccountname'][0] ?? $entry['cn'][0] ?? $entry['name'][0] ?? null;

                                    // NIK detection
                                    $nik = $entry['employeeid'][0] 
                                            ?? $entry['extensionattribute1'][0] 
                                            ?? $entry['description'][0] 
                                            ?? null;

                                    $users[] = [
                                        'username' => $username,
                                        'name' => $entry['cn'][0] ?? $entry['name'][0] ?? $username,
                                        'email' => $entry['mail'][0] ?? null,
                                        'guid' => $guid,
                                        'nik' => $nik,
                                        'department' => $entry['department'][0] ?? '-',
                                        'position' => $entry['title'][0] ?? '-',
                                        'phone' => $entry['telephonenumber'][0] ?? null,
                                        'raw_keys' => array_keys($entry)
                                    ];
                                }
                            }
                            
                            // Get cookie for next page
                            @\ldap_control_paged_result_response($conn, $search, $cookie);
                        } else {
                            $cookie = null;
                        }
                    } catch (\Throwable $e) {
                        \Log::error("LDAP Search error in DN {$dn}: " . $e->getMessage());
                        $cookie = null;
                    }
                } while ($cookie !== null && $cookie != '');
            }
            return $users;
        }

        \Log::error("LDAP Bind failed for admin user: " . $adminUser . " Error: " . \ldap_error($conn));
        return [];
    }

    /**
     * Get a single user's detail by sAMAccountName
     */
    public function getUserByUsername($username)
    {
        $conn = $this->connect();
        $adminUser = config('services.ldap.admin_user');
        $adminPass = config('services.ldap.admin_pass');

        if (@\ldap_bind($conn, $adminUser, $adminPass)) {
            $filter = "(&(objectClass=user)(samaccountname=$username))";
            $attributes = ['samaccountname', 'cn', 'name', 'mail', 'objectguid', 'dn'];
            
            try {
                $search = @\ldap_search($conn, $this->baseDn, $filter, $attributes);

                if ($search) {
                    $entries = @\ldap_get_entries($conn, $search);
                    if ($entries !== false && $entries['count'] > 0) {
                        return [
                            'username' => $entries[0]['samaccountname'][0] ?? null,
                            'name' => $entries[0]['cn'][0] ?? $entries[0]['name'][0] ?? null,
                            'email' => $entries[0]['mail'][0] ?? null,
                            'guid' => bin2hex($entries[0]['objectguid'][0]) ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Mencegah crash jika LDAP error
            }
        }

        return null;
    }
}
