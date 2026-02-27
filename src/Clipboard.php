<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class Clipboard
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function read(): string
	{
		$result = $this->client->get('api/clipboard/read');

		return isset($result['text']) && is_string($result['text']) ? $result['text'] : '';
	}

	public function write(string $text): void
	{
		$this->client->post('api/clipboard/write', ['text' => $text]);
	}

	public function clear(): void
	{
		$this->client->post('api/clipboard/clear');
	}
}
