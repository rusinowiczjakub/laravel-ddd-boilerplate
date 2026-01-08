<?php

namespace App\Console\Commands\Command;

use Illuminate\Console\Command;
use Modules\Core\Command\Contracts\CommandDiscovery;

class Discover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commands:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover events marked with #[Subscribe] attribute';

    /**
     * Execute the console command.
     */
    public function handle(CommandDiscovery $discovery)
    {
        $discovery->discover();
    }
}
