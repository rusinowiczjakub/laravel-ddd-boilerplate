<?php

namespace App\Console\Commands\Queries;

use Illuminate\Console\Command;
use Modules\Core\Query\Contracts\QueryDiscovery;

class Discover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queries:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover events marked with #[Subscribe] attribute';

    /**
     * Execute the console command.
     */
    public function handle(QueryDiscovery $discovery)
    {
        $discovery->discover();
    }
}
