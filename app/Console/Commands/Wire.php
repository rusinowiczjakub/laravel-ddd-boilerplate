<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\ImageGenerator\Infrastructure\Service\ImagickMockupImageGenerator;

class Wire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        ImagickMockupImageGenerator $mockupImageGenerator
    ) {
        $mockupImageGenerator->generate(
            base_path('img.png'),
            base_path()
        );
    }
}
