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
            $search = \ldap_search($conn, $this->baseDn, $filter, ['dn']);
            $entries = \ldap_get_entries($conn, $search);

            if ($entries['count'] > 0) {
                return $entries[0]['dn'];
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
            $filter = "(objectClass=user)";
            $attributes = ['samaccountname', 'cn', 'name', 'mail', 'objectguid', 'dn'];
            $search = \ldap_search($conn, $this->baseDn, $filter, $attributes);
            $entries = \ldap_get_entries($conn, $search);

            $users = [];
            for ($i = 0; $i < $entries['count']; $i++) {
                $users[] = [
                    'username' => $entries[$i]['samaccountname'][0] ?? null,
                    'name' => $entries[$i]['cn'][0] ?? $entries[$i]['name'][0] ?? null,
                    'email' => $entries[$i]['mail'][0] ?? null,
                    'guid' => bin2hex($entries[$i]['objectguid'][0]) ?? null,
                ];
            }
            return $users;
        }

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
            $search = \ldap_search($conn, $this->baseDn, $filter, $attributes);
            $entries = \ldap_get_entries($conn, $search);

            if ($entries['count'] > 0) {
                return [
                    'username' => $entries[0]['samaccountname'][0] ?? null,
                    'name' => $entries[0]['cn'][0] ?? $entries[0]['name'][0] ?? null,
                    'email' => $entries[0]['mail'][0] ?? null,
                    'guid' => bin2hex($entries[0]['objectguid'][0]) ?? null,
                ];
            }
        }

        return null;
    }
}
