<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class App
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function quit(): void
	{
		$this->client->post('api/app/quit');
	}

	public function relaunch(): void
	{
		$this->client->post('api/app/relaunch');
	}

	public function hide(): void
	{
		$this->client->post('api/app/hide');
	}

	public function show(): void
	{
		$this->client->post('api/app/show');
	}
}
