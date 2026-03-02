<?php

namespace Bulbalara\CoreConfigMs\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'config-ms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the core config for Moonshine package';

    public function handle(): void
    {
        $this->installConfigPackage();
        $this->initDatabase();
    }

    protected function installConfigPackage(): void
    {
        $this->call('coreconfig:install');
    }

    protected function initDatabase(): void
    {
        $this->call('migrate', [
            '--path' => __DIR__.'/../../database/migrations/',
            '--realpath' => true,
        ]);

        $this->call('db:seed', [
            '--class' => \Bulbalara\CoreConfigMs\Database\ConfigSeeder::class,
        ]);
    }
}
