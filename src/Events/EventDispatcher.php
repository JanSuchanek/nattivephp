<?php

declare(strict_types=1);

namespace NativePHP\Nette\Events;

/**
 * Simple event dispatcher for NativePHP events.
 * Resolves event class from Electron event string and dispatches to registered handlers.
 *
 * @see Electron: Events are sent via POST to /_native/api/events with {event, payload}
 */
class EventDispatcher
{
	/** @var array<string, list<callable>> */
	private array $handlers = [];

	/**
	 * Register a handler for a specific event class.
	 *
	 * @param class-string<NativeEvent> $eventClass
	 */
	public function on(string $eventClass, callable $handler): void
	{
		$this->handlers[$eventClass][] = $handler;
	}

	/**
	 * Dispatch an event from Electron's event string + payload.
	 *
	 * @param string $eventString e.g. '\\Native\\Nette\\Events\\Windows\\WindowShown'
	 * @param array<string, mixed> $payload
	 */
	public function dispatch(string $eventString, array $payload = []): void
	{
		// Normalize: Electron sends \\Native\\Nette\\Events\\... → NativePHP\\Nette\\Events\\...
		$class = str_replace('Native\\Nette\\Events\\', 'NativePHP\\Nette\\Events\\', $eventString);
		$class = ltrim($class, '\\');

		if (!class_exists($class) || !is_subclass_of($class, NativeEvent::class)) {
			return;
		}

		$event = new $class($payload);

		foreach ($this->handlers[$class] ?? [] as $handler) {
			$handler($event);
		}
	}
}
