<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Auto-updater for Electron app updates.
 *
 * @see Electron: electron-plugin/src/server/api/autoUpdater.ts
 * @see Laravel: NativePHP\Laravel\Facades\AutoUpdater
 * @api POST /api/auto-updater/{check-for-updates,download-update,quit-and-install}
 */
class AutoUpdater
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Check for available updates.
	 */
	public function checkForUpdates(): void
	{
		$this->client->post('auto-updater/check-for-updates');
	}

	/**
	 * Download the available update.
	 */
	public function downloadUpdate(): void
	{
		$this->client->post('auto-updater/download-update');
	}

	/**
	 * Quit the app and install the downloaded update.
	 */
	public function quitAndInstall(): void
	{
		$this->client->post('auto-updater/quit-and-install');
	}
}
