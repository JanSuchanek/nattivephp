<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Spawn and manage child processes.
 *
 * @see Electron: electron-plugin/src/server/api/childProcess.ts
 * @see Laravel: NativePHP\Laravel\Facades\ChildProcess
 * @api POST|GET /api/child-process/{start,start-php,stop,restart,get/:alias,message}
 */
class ChildProcess
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Start a child process.
	 *
	 * @param array{alias: string, cmd: array<string>, cwd?: string, env?: array<string, string>, persistent?: bool} $settings
	 * @return array<string, mixed> Process info
	 */
	public function start(array $settings): array
	{
		return $this->client->post('child-process/start', $settings);
	}

	/**
	 * Start a PHP child process.
	 *
	 * @param array{alias: string, cmd: array<string>, cwd?: string, env?: array<string, string>, persistent?: bool} $settings
	 * @return array<string, mixed> Process info
	 */
	public function startPhp(array $settings): array
	{
		return $this->client->post('child-process/start-php', $settings);
	}

	/**
	 * Stop a child process by alias.
	 */
	public function stop(string $alias): void
	{
		$this->client->post('child-process/stop', ['alias' => $alias]);
	}

	/**
	 * Restart a child process by alias.
	 *
	 * @return array<string, mixed> Process info
	 */
	public function restart(string $alias): array
	{
		return $this->client->post('child-process/restart', ['alias' => $alias]);
	}

	/**
	 * Get info about a running child process.
	 *
	 * @return array<string, mixed>|null Process info or null
	 */
	public function get(string $alias): ?array
	{
		$result = $this->client->get("child-process/get/{$alias}");

		return $result ?: null;
	}

	/**
	 * Get all running child processes.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array
	{
		return $this->client->get('child-process');
	}

	/**
	 * Send a message to a child process.
	 */
	public function message(string $alias, mixed $message): void
	{
		$this->client->post('child-process/message', ['alias' => $alias, 'message' => $message]);
	}
}
