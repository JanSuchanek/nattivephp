<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Window management – open, close, resize, focus native windows.
 */
class Window
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function open(
		string $id,
		string $url,
		int $width = 800,
		int $height = 600,
		string $title = '',
		bool $resizable = true,
		bool $frame = true,
		?int $x = null,
		?int $y = null,
		?int $minWidth = null,
		?int $minHeight = null,
		?int $maxWidth = null,
		?int $maxHeight = null,
		bool $alwaysOnTop = false,
		bool $fullscreen = false,
		bool $kiosk = false,
		string $titleBarStyle = 'default',
		?string $vibrancy = null,
		?string $backgroundColor = null,
		bool $transparency = false,
		bool $showDevTools = false,
		float $zoomFactor = 1.0,
	): void {
		$this->client->post('window/open', array_filter([
			'id' => $id,
			'url' => $url,
			'width' => $width,
			'height' => $height,
			'title' => $title,
			'resizable' => $resizable,
			'frame' => $frame,
			'x' => $x,
			'y' => $y,
			'minWidth' => $minWidth,
			'minHeight' => $minHeight,
			'maxWidth' => $maxWidth,
			'maxHeight' => $maxHeight,
			'alwaysOnTop' => $alwaysOnTop,
			'fullscreen' => $fullscreen,
			'kiosk' => $kiosk,
			'titleBarStyle' => $titleBarStyle,
			'vibrancy' => $vibrancy,
			'backgroundColor' => $backgroundColor,
			'transparency' => $transparency,
			'showDevTools' => $showDevTools,
			'zoomFactor' => $zoomFactor,
			'movable' => true,
			'minimizable' => true,
			'maximizable' => true,
			'closable' => true,
			'focusable' => true,
		], static fn(mixed $v): bool => $v !== null));
	}

	public function close(string $id): void
	{
		$this->client->post('window/close', ['id' => $id]);
	}

	public function resize(string $id, int $width, int $height): void
	{
		$this->client->post('window/resize', ['id' => $id, 'width' => $width, 'height' => $height]);
	}

	public function title(string $id, string $title): void
	{
		$this->client->post('window/title', ['id' => $id, 'title' => $title]);
	}

	public function position(string $id, int $x, int $y): void
	{
		$this->client->post('window/position', ['id' => $id, 'x' => $x, 'y' => $y]);
	}

	public function maximize(string $id): void
	{
		$this->client->post('window/maximize', ['id' => $id]);
	}

	public function minimize(string $id): void
	{
		$this->client->post('window/minimize', ['id' => $id]);
	}

	public function reload(string $id): void
	{
		$this->client->post('window/reload', ['id' => $id]);
	}

	public function hide(string $id): void
	{
		$this->client->post('window/hide', ['id' => $id]);
	}

	public function show(string $id): void
	{
		$this->client->post('window/show', ['id' => $id]);
	}

	public function alwaysOnTop(string $id, bool $alwaysOnTop = true): void
	{
		$this->client->post('window/always-on-top', ['id' => $id, 'alwaysOnTop' => $alwaysOnTop]);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function current(): array
	{
		return $this->client->get('window/current');
	}

	/**
	 * @return array<string, mixed>
	 */
	public function all(): array
	{
		return $this->client->get('window/all');
	}
}
