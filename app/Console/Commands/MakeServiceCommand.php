<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Tạo một Service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("Service {$name} đã tồn tại.");
            return;
        }

        File::ensureDirectoryExists(app_path('Services'));

        File::put($path, <<<EOT
        <?php

            namespace App\Services;

            class {$name} extends BaseService
            {
                public function __construct()
                {
                    //
                }
            }
        EOT);
        $this->line("📄 File: " . realpath($path));
    }
}
