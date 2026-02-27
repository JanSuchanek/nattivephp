<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class Screen
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * @return array<string, mixed>
	 */
	public function primaryDisplay(): array
	{
		return $this->client->get('api/screen/primary-display');
	}

	/**
	 * @return array<string, mixed>
	 */
	public function allDisplays(): array
	{
		return $this->client->get('api/screen/all-displays');
	}

	/**
	 * @return array<string, mixed>
	 */
	public function cursorPosition(): array
	{
		return $this->client->get('api/screen/cursor-position');
	}
}
