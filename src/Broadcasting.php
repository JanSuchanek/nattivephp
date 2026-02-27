<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Broadcast events to all Electron windows via IPC.
 *
 * @see Electron: electron-plugin/src/server/api/broadcasting.ts
 * @see Laravel: NativePHP\Laravel\Broadcasting
 * @api POST /api/broadcast
 */
class Broadcasting
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Broadcast an event with payload to all windows.
	 *
	 * @param array<string, mixed> $payload
	 */
	public function send(string $event, array $payload = []): void
	{
		$this->client->post('broadcast', ['event' => $event, 'payload' => $payload]);
	}
}
