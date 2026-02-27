<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Right-click context menu.
 *
 * @see Electron: electron-plugin/src/server/api/contextMenu.ts
 * @see Laravel: NativePHP\Laravel\Facades\ContextMenu
 * @api POST|DELETE /api/context
 */
class ContextMenu
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Set the right-click context menu entries.
	 *
	 * @param array<int, array{label?: string, role?: string, type?: string, click?: string, accelerator?: string}> $entries
	 */
	public function set(array $entries): void
	{
		$this->client->post('context', ['entries' => $entries]);
	}

	/**
	 * Remove the custom context menu (restore browser default).
	 */
	public function remove(): void
	{
		$this->client->delete('context');
	}
}
