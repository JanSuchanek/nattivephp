<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * macOS Dock manipulation (badge, bounce, icon, menu).
 *
 * @see Electron: electron-plugin/src/server/api/dock.ts
 * @see Laravel: NativePHP\Laravel\Facades\Dock
 * @api POST|GET /api/dock/{show,hide,icon,bounce,cancel-bounce,badge}
 */
class Dock
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Set the dock menu items.
	 *
	 * @param array<int, array<string, mixed>> $items
	 */
	public function menu(array $items): void
	{
		$this->client->post('dock', ['items' => $items]);
	}

	public function show(): void
	{
		$this->client->post('dock/show');
	}

	public function hide(): void
	{
		$this->client->post('dock/hide');
	}

	public function icon(string $path): void
	{
		$this->client->post('dock/icon', ['path' => $path]);
	}

	/**
	 * Bounce the dock icon.
	 *
	 * @param string $type 'critical' or 'informational'
	 */
	public function bounce(string $type = 'informational'): void
	{
		$this->client->post('dock/bounce', ['type' => $type]);
	}

	public function cancelBounce(): void
	{
		$this->client->post('dock/cancel-bounce');
	}

	public function getBadge(): string
	{
		$result = $this->client->get('dock/badge');

		return isset($result['label']) && is_string($result['label']) ? $result['label'] : '';
	}

	public function setBadge(string $label): void
	{
		$this->client->post('dock/badge', ['label' => $label]);
	}
}
