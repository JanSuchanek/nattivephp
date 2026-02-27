<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Debug logging to Electron DevTools and all windows.
 *
 * @see Electron: electron-plugin/src/server/api/debug.ts
 * @see Laravel: NativePHP\Laravel\Facades\Debug
 * @api POST /api/debug/log
 */
class Debug
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Send a log message to all Electron windows.
	 *
	 * @param string $level 'info'|'warning'|'error'|'debug'
	 * @param array<string, mixed> $context
	 */
	public function log(string $level, string $message, array $context = []): void
	{
		$this->client->post('debug/log', ['level' => $level, 'message' => $message, 'context' => $context]);
	}

	public function info(string $message, array $context = []): void
	{
		$this->log('info', $message, $context);
	}

	public function warning(string $message, array $context = []): void
	{
		$this->log('warning', $message, $context);
	}

	public function error(string $message, array $context = []): void
	{
		$this->log('error', $message, $context);
	}
}
