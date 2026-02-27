<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Register/unregister global keyboard shortcuts.
 *
 * @see Electron: electron-plugin/src/server/api/globalShortcut.ts
 * @see Laravel: NativePHP\Laravel\Facades\GlobalShortcut
 * @api POST|DELETE|GET /api/global-shortcuts
 */
class GlobalShortcut
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Register a global shortcut.
	 *
	 * @param string $key Accelerator string, e.g. 'CommandOrControl+X'
	 * @param string $event PHP event class dispatched when shortcut is triggered
	 */
	public function register(string $key, string $event): void
	{
		$this->client->post('global-shortcuts', ['key' => $key, 'event' => $event]);
	}

	/**
	 * Unregister a global shortcut.
	 */
	public function unregister(string $key): void
	{
		$this->client->delete('global-shortcuts', ['key' => $key]);
	}

	/**
	 * Check if a shortcut is registered.
	 */
	public function isRegistered(string $key): bool
	{
		$result = $this->client->get("global-shortcuts/{$key}");

		return isset($result['isRegistered']) && $result['isRegistered'] === true;
	}
}
