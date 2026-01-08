<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Core\Attributes\Subscribe;
use Modules\Core\Events\Contracts\AsyncListener;
use Modules\Core\Events\Contracts\EventDiscovery;
use Modules\Core\Events\QueueableListenerWrapper;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class FileCacheEventDiscovery extends AttributeClassDiscovery implements EventDiscovery
{
    public function __construct(string $discoveryPath, string $cachePath, bool $shouldDiscover)
    {
        parent::__construct($discoveryPath, $cachePath, $shouldDiscover, Subscribe::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(?callable $registerCallback = null): void
    {
        $eventsPath = storage_path($this->cachePath);

        if (! file_exists($eventsPath)) {
            $this->discover();
        }

        if (file_exists($eventsPath)) {
            $eventMap = include $eventsPath;

            foreach ($eventMap as $event => $listeners) {
                foreach ($listeners as $listener) {
                    // Check if listener should be async
                    $listenerInstance = app()->make($listener);
                    $shouldQueue = $listenerInstance instanceof AsyncListener;

                    $actualListener = $shouldQueue
                        ? function ($event) use ($listener, $listenerInstance) {
                            /** @var AsyncListener $listenerInstance */
                            dispatch((new QueueableListenerWrapper($listener, $event))->onQueue($listenerInstance->viaQueue()));
                        }
                    : $listener;

                    if ($registerCallback) {
                        $registerCallback($event, $actualListener);
                    } else {
                        Event::listen($event, $actualListener);
                    }
                }
            }
        }
    }

    public function discover(?string $path = null): void
    {
        $rootPath = base_path($path ?? $this->discoveryPath);

        if (! is_dir($rootPath) || ! $this->shouldDiscover) {
            return;
        }

        $finder = (new Finder())->files()->in($rootPath)->name('*.php');

        // event => [ ['class'=>ListenerA::class, 'priority'=>0], ... ]
        $map = [];

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());
            if (! $className || ! class_exists($className)) {
                continue;
            }

            $ref = new ReflectionClass($className);
            $atts = $ref->getAttributes($this->discoverAttribute);

            foreach ($atts as $att) {
                /** @var Subscribe $subscribe */
                $subscribe = $att->newInstance();

                $targets = is_array($subscribe->target) ? $subscribe->target : [$subscribe->target];

                foreach ($targets as $event) {
                    if (! class_exists($event) && ! interface_exists($event)) {
                        continue;
                    }
                    $map[$event] ??= [];
                    $map[$event][] = [
                        'class' => $className,
                        'priority' => $subscribe->priority,
                    ];
                }
            }
        }

        foreach ($map as $event => $listeners) {
            usort($listeners, fn ($a, $b) => $b['priority'] <=> $a['priority']);
            $map[$event] = array_values(array_unique(
                array_map(fn ($x) => $x['class'], $listeners)
            ));
        }

        file_put_contents(
            storage_path($this->cachePath),
            $this->createCacheContent($map)
        );
    }
}
