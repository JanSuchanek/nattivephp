<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * System tray (menu bar) with icon, tooltip, label, and optional window.
 *
 * @see Electron: electron-plugin/src/server/api/menuBar.ts
 * @see Laravel: NativePHP\Laravel\Facades\MenuBar
 * @api POST /api/menu-bar/{create,label,tooltip,icon,context-menu,show,hide,resize}
 */
class MenuBar
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Create a system tray icon/menubar entry.
	 *
	 * @param array{
	 *   width?: int,
	 *   height?: int,
	 *   url?: string,
	 *   label?: string,
	 *   alwaysOnTop?: bool,
	 *   vibrancy?: string,
	 *   backgroundColor?: string,
	 *   transparency?: bool,
	 *   icon?: string,
	 *   showDockIcon?: bool,
	 *   onlyShowContextMenu?: bool,
	 *   windowPosition?: string,
	 *   showOnAllWorkspaces?: bool,
	 *   contextMenu?: array<mixed>,
	 *   tooltip?: string,
	 *   resizable?: bool,
	 * } $options
	 */
	public function create(array $options = []): void
	{
		$this->client->post('menu-bar/create', $options);
	}

	public function label(string $label): void
	{
		$this->client->post('menu-bar/label', ['label' => $label]);
	}

	public function tooltip(string $tooltip): void
	{
		$this->client->post('menu-bar/tooltip', ['tooltip' => $tooltip]);
	}

	public function icon(string $icon): void
	{
		$this->client->post('menu-bar/icon', ['icon' => $icon]);
	}

	/**
	 * @param array<int, array<string, mixed>> $contextMenu
	 */
	public function contextMenu(array $contextMenu): void
	{
		$this->client->post('menu-bar/context-menu', ['contextMenu' => $contextMenu]);
	}

	public function show(): void
	{
		$this->client->post('menu-bar/show');
	}

	public function hide(): void
	{
		$this->client->post('menu-bar/hide');
	}

	public function resize(int $width, int $height): void
	{
		$this->client->post('menu-bar/resize', ['width' => $width, 'height' => $height]);
	}
}
