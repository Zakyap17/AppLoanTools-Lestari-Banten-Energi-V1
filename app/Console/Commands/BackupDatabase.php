<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the PostgreSQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup_' . env('DB_DATABASE') . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '.sql';
        $storagePath = storage_path('app/backups');
        
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $command = sprintf(
            '"%s" --host=%s --port=%s --username=%s --format=c %s --file="%s"',
            env('DB_DUMP_PATH', 'C:\Program Files\PostgreSQL\18\bin\pg_dump.exe'),
            env('DB_HOST', '127.0.0.1'),
            env('DB_PORT', '5432'),
            env('DB_USERNAME', 'postgres'),
            env('DB_DATABASE'),
            $storagePath . '/' . $filename
        );
        
        $this->info('Starting database backup: ' . $filename);

        try {
            $process = Process::fromShellCommandline($command, null, [
                'PGPASSWORD' => env('DB_PASSWORD')
            ]);
            $process->setTimeout(null); // Backups can take a while
            $process->mustRun();

            $this->info('Database backup completed successfully. Saved to: ' . $storagePath . '/' . $filename);
        } catch (ProcessFailedException $exception) {
            $this->error('Database backup failed: ' . $exception->getMessage());
        } catch (\Exception $e) {
            $this->error('An error occurred during backup: ' . $e->getMessage());
        }
    }
}
