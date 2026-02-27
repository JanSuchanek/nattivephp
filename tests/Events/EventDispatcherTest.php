<?php

declare(strict_types=1);

use NativePHP\Nette\Events\EventDispatcher;
use NativePHP\Nette\Events\Windows\WindowFocused;
use NativePHP\Nette\Events\MenuBar\MenuBarClicked;
use NativePHP\Nette\Events\NativeEvent;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class EventDispatcherTest extends TestCase
{
	public function testDispatchesEvent(): void
	{
		$dispatcher = new EventDispatcher();
		$received = null;
		$dispatcher->on(WindowFocused::class, function (NativeEvent $event) use (&$received) {
			$received = $event;
		});

		$dispatcher->dispatch('\\Native\\Nette\\Events\\Windows\\WindowFocused', ['windowId' => 'main']);

		Assert::type(WindowFocused::class, $received);
		Assert::same('main', $received->payload['windowId']);
	}

	public function testIgnoresUnknownEvent(): void
	{
		$dispatcher = new EventDispatcher();
		$called = false;
		$dispatcher->on(WindowFocused::class, function () use (&$called) { $called = true; });

		$dispatcher->dispatch('\\Native\\Nette\\Events\\Windows\\NonExistent');

		Assert::false($called);
	}

	public function testMultipleHandlers(): void
	{
		$dispatcher = new EventDispatcher();
		$count = 0;
		$dispatcher->on(MenuBarClicked::class, function () use (&$count) { $count++; });
		$dispatcher->on(MenuBarClicked::class, function () use (&$count) { $count++; });

		$dispatcher->dispatch('\\Native\\Nette\\Events\\MenuBar\\MenuBarClicked');

		Assert::same(2, $count);
	}

	public function testPayloadPassedToEvent(): void
	{
		$dispatcher = new EventDispatcher();
		$payload = null;
		$dispatcher->on(MenuBarClicked::class, function (NativeEvent $e) use (&$payload) {
			$payload = $e->payload;
		});

		$dispatcher->dispatch('\\Native\\Nette\\Events\\MenuBar\\MenuBarClicked', ['x' => 100, 'y' => 200]);

		Assert::same(100, $payload['x']);
		Assert::same(200, $payload['y']);
	}
}

(new EventDispatcherTest)->run();
