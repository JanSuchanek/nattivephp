<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Native application menu.
 *
 * @see Electron: electron-plugin/src/server/api/menu.ts
 * @see Laravel: NativePHP\Laravel\Facades\Menu
 * @api POST /api/menu
 */
class Menu
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Set the application menu from an array of menu items.
	 *
	 * @param array<int, array{label?: string, role?: string, type?: string, submenu?: array<mixed>, accelerator?: string, click?: string}> $items
	 */
	public function set(array $items): void
	{
		$this->client->post('menu', ['items' => $items]);
	}
}
