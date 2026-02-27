<?php

declare(strict_types=1);

namespace NativePHP\Nette;

class Dialog
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * @param array<string, string[]> $filters e.g. [['name' => 'Images', 'extensions' => ['jpg', 'png']]]
	 * @return array<string, mixed>
	 */
	public function open(
		string $title = 'Open',
		?string $defaultPath = null,
		array $filters = [],
		bool $multiSelections = false,
	): array {
		return $this->client->get('dialog/open', array_filter([
			'title' => $title,
			'defaultPath' => $defaultPath,
			'filters' => $filters === [] ? null : json_encode($filters),
			'multiSelections' => $multiSelections,
		], fn(string|bool|null $v): bool => $v !== null));
	}

	/**
	 * @return array<string, mixed>
	 */
	public function save(
		string $title = 'Save',
		?string $defaultPath = null,
	): array {
		return $this->client->get('dialog/save', array_filter([
			'title' => $title,
			'defaultPath' => $defaultPath,
		], fn(?string $v): bool => $v !== null));
	}
}
