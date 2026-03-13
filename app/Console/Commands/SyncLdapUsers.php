<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LdapService;

class SyncLdapUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize LDAP users with the local database (Names, Emails, and Status)';

    /**
     * Execute the console command.
     */
    public function handle(LdapService $ldapService)
    {
        if (!config('services.ldap.enabled')) {
            $this->warn('LDAP synchronization is disabled in .env. Skipping...');
            return;
        }

        $this->info('Starting Native LDAP synchronization...');

        // 1. Fetch all LDAP users using native service
        $ldapUsers = $ldapService->getAllUsers();
        $ldapGuids = [];

        foreach ($ldapUsers as $ldapUser) {
            $ldapGuids[] = $ldapUser['guid'];

            // Find or update local user
            $user = \App\Models\User::updateOrCreate(
                ['guid' => $ldapUser['guid']],
                [
                    'name' => $ldapUser['name'],
                    'username' => $ldapUser['username'],
                    'is_active' => true,
                ]
            );

            $this->info("Synced user: {$user->username}");
        }

        // 2. Disable local users no longer in LDAP (or Base DN)
        \App\Models\User::whereNotIn('guid', $ldapGuids)
            ->whereNotNull('guid')
            ->update(['is_active' => false]);

        $this->info('Native LDAP synchronization completed.');
    }
}
