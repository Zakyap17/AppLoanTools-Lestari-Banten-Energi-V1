<?php

namespace App\Services;

class LdapService
{
    protected $connection;
    protected $baseDn;

    public function __construct()
    {
        $this->baseDn = env('LDAP_BASE_DN');
    }

    /**
     * Connect to LDAP server
     */
    protected function connect()
    {
        $host = env('LDAP_HOST');
        $port = env('LDAP_PORT', 389);
        $protocol = ($port == 636) ? 'ldaps://' : 'ldap://';

        $this->connection = \ldap_connect($protocol . $host, $port);

        if (env('LDAP_IGNORE_CERT', false)) {
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
        $adminUser = env('LDAP_ADMIN_USER');
        $adminPass = env('LDAP_ADMIN_PASS');

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
        $adminUser = env('LDAP_ADMIN_USER');
        $adminPass = env('LDAP_ADMIN_PASS');

        if (@\ldap_bind($conn, $adminUser, $adminPass)) {
            \Log::info("LDAP Bind successful for admin user: " . $adminUser);
            $filter = "(objectClass=user)";
            $attributes = ['samaccountname', 'cn', 'name', 'mail', 'objectguid', 'dn', 'employeeid', 'department', 'title', 'telephonenumber'];
            
            $users = [];
            try {
                $search = @\ldap_search($conn, $this->baseDn, $filter, $attributes);

                if ($search) {
                    $entries = @\ldap_get_entries($conn, $search);
                    if ($entries !== false) {
                        for ($i = 0; $i < $entries['count']; $i++) {
                            $users[] = [
                                'username' => $entries[$i]['samaccountname'][0] ?? null,
                                'name' => $entries[$i]['cn'][0] ?? $entries[$i]['name'][0] ?? null,
                                'email' => $entries[$i]['mail'][0] ?? null,
                                'guid' => isset($entries[$i]['objectguid'][0]) ? bin2hex($entries[$i]['objectguid'][0]) : null,
                                'nik' => $entries[$i]['employeeid'][0] ?? null,
                                'department' => $entries[$i]['department'][0] ?? null,
                                'position' => $entries[$i]['title'][0] ?? null,
                                'phone' => $entries[$i]['telephonenumber'][0] ?? null,
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::error("LDAP Search error: " . $e->getMessage());
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
        $adminUser = env('LDAP_ADMIN_USER');
        $adminPass = env('LDAP_ADMIN_PASS');

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
