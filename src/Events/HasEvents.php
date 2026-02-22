<?php

namespace Tonsoo\SitemapGenerator\Events;

use Tonsoo\SitemapGenerator\Extensions\CrawlerExtensionInterface;

/**
 * @template T
 */
trait HasEvents
{
    /**
     * @template T
     * @var array<T, callable(T): void> $listeners
     */
    private array $listeners = [];

    /**
     * @param class-string<T> $eventClass
     * @param callable(T): void $callback
     * @return $this
     */
    public function on(string $eventClass, callable $callback): self
    {
        $this->listeners[$eventClass] ??= [];
        $this->listeners[$eventClass][] = $callback;
        return $this;
    }

    /**
     * @param callable(OnStart): void $callback
     * @return self
     */
    public function onStart(callable $callback): self
    {
        return $this->on(OnStart::class, $callback);
    }

    /**
     * @param callable(OnCrawled): void $callback
     * @return self
     */
    public function onCrawled(callable $callback): self
    {
        return $this->on(OnCrawled::class, $callback);
    }

    /**
     * @param callable(OnLinkFound): void $callback
     * @return self
     */
    public function onLinkFound(callable $callback): self
    {
        return $this->on(OnLinkFound::class, $callback);
    }

    /**
     * @param callable(OnMismatchContent): void $callback
     * @return self
     */
    public function onMismatchContent(callable $callback): self
    {
        return $this->on(OnMismatchContent::class, $callback);
    }

    /**
     * @param callable(OnMissingHtmlBody): void $callback
     * @return self
     */
    public function onMissingHtmlBody(callable $callback): self
    {
        return $this->on(OnMissingHtmlBody::class, $callback);
    }

    /**
     * @param callable(OnFinish): void $callback
     * @return self
     */
    public function onFinish(callable $callback): self
    {
        return $this->on(OnFinish::class, $callback);
    }

    public function subscribe(CrawlerExtensionInterface $extension): self
    {
        $extension->subscribe($this);
        return $this;
    }

    /**
     * @param T $event
     * @return void
     */
    public function dispatch($event): void
    {
        $class = $event::class;
        $callbackList = $this->listeners[$class] ?? null;
        if (!is_array($callbackList)) {
            return;
        }

        foreach ($callbackList as $callback) {
            $callback($event);
        }
    }
}