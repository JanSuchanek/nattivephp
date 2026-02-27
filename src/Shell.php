<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class Shell
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function openExternal(string $url): void
	{
		$this->client->post('shell/open-external', ['url' => $url]);
	}

	public function showItemInFolder(string $path): void
	{
		$this->client->post('shell/show-item-in-folder', ['path' => $path]);
	}

	public function trashItem(string $path): void
	{
		$this->client->post('shell/trash-item', ['path' => $path]);
	}
}
