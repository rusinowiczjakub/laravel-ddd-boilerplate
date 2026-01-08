<?php

namespace App\Console\Commands\Events;

use Illuminate\Console\Command;
use Modules\Core\Events\Contracts\EventDiscovery;

class Discover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover events marked with #[Subscribe] attribute';

    /**
     * Execute the console command.
     */
    public function handle(EventDiscovery $discovery)
    {
        $discovery->discover();
    }
}
