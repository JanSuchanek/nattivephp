<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Taskbar/dock progress bar.
 *
 * @see Electron: electron-plugin/src/server/api/progressBar.ts
 * @see Laravel: NativePHP\Laravel\Facades\ProgressBar
 * @api POST /api/progress-bar/update
 */
class ProgressBar
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Update the progress bar on all windows.
	 *
	 * @param float $percent Progress value (0.0 to 1.0), or -1 to remove
	 */
	public function update(float $percent): void
	{
		$this->client->post('progress-bar/update', ['percent' => $percent]);
	}
}
