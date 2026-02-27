<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Persistent key-value settings store (electron-store).
 *
 * @see Electron: electron-plugin/src/server/api/settings.ts
 * @see Laravel: NativePHP\Laravel\Facades\Settings
 * @api GET|POST|DELETE /api/settings/{:key}
 */
class Settings
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function get(string $key): mixed
	{
		$result = $this->client->get("settings/{$key}");

		return $result['value'] ?? null;
	}

	public function set(string $key, mixed $value): void
	{
		$this->client->post("settings/{$key}", ['value' => $value]);
	}

	public function delete(string $key): void
	{
		$this->client->delete("settings/{$key}");
	}

	public function clear(): void
	{
		$this->client->delete('settings');
	}
}
