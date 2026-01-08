<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Modules\Core\Contracts\Discovery;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

abstract class AttributeClassDiscovery implements Discovery
{
    public function __construct(
        protected string $discoveryPath,
        protected string $cachePath,
        protected bool $shouldDiscover,
        protected string $discoverAttribute
    ) {
    }

    public function discover(?string $path = null): void
    {
        $rootPath = base_path($path ?? $this->discoveryPath);

        if (! is_dir($rootPath) || ! $this->shouldDiscover) {
            return;
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($rootPath)
            ->name('*.php');

        $map = [];

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());

            if (! $className || ! class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            $attributes = $reflection->getAttributes($this->discoverAttribute);

            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();

                if (! class_exists($instance->target) && ! interface_exists($instance->target)) {
                    continue;
                }

                $map[$instance->target] = $className;
            }
        }

        file_put_contents(
            storage_path($this->cachePath),
            $this->createCacheContent($map)
        );
    }

    abstract public function boot(?callable $registerCallback = null): void;

    protected function getClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        if (
            preg_match('/namespace\s+(.+?);/s', $contents, $namespaceMatch) &&
            preg_match('/class\s+(\w+)/s', $contents, $classMatch)
        ) {
            return $namespaceMatch[1] . '\\' . $classMatch[1];
        }

        return null;
    }

    protected function createCacheContent(array $eventMap): string
    {
        $json = json_encode($eventMap, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return "<?php return json_decode('" . str_replace('\\', '\\\\', $json) . "', true);";
    }
}
