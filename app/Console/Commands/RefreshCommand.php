<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo shell_exec("php artisan migrate:fresh --seed");
        echo shell_exec("php artisan passport:install");

        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public/brands');
        $file->cleanDirectory('storage/app/public/categories');
        $file->cleanDirectory('storage/app/public/collections');
        $file->cleanDirectory('storage/app/public/products');
        $file->cleanDirectory('storage/app/public/seasons');

        return true;
    }
}
