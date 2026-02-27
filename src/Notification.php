<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class Notification
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function send(string $title, string $body = ''): void
	{
		$this->client->post('notification', [
			'title' => $title,
			'body' => $body,
		]);
	}
}
