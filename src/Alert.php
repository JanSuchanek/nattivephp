<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Native alert/message boxes and error dialogs.
 *
 * @see Electron: electron-plugin/src/server/api/alert.ts
 * @see Laravel: NativePHP\Laravel\Facades\Alert
 * @api POST /api/alert/{message,error}
 */
class Alert
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Show a message box dialog.
	 *
	 * @param string $message Main message
	 * @param string|null $type 'none'|'info'|'error'|'question'|'warning'
	 * @param string|null $title Window title
	 * @param string|null $detail Additional detail text
	 * @param array<int, string>|null $buttons Custom button labels
	 * @param int|null $defaultId Index of the default button
	 * @param int|null $cancelId Index of the cancel button
	 * @return int Index of clicked button
	 */
	public function message(
		string $message,
		?string $type = null,
		?string $title = null,
		?string $detail = null,
		?array $buttons = null,
		?int $defaultId = null,
		?int $cancelId = null,
	): int {
		$result = $this->client->post('alert/message', array_filter([
			'message' => $message,
			'type' => $type,
			'title' => $title,
			'detail' => $detail,
			'buttons' => $buttons,
			'defaultId' => $defaultId,
			'cancelId' => $cancelId,
		], fn (string|int|array|null $v): bool => $v !== null));

		return isset($result['result']) && is_int($result['result']) ? $result['result'] : 0;
	}

	/**
	 * Show an error dialog.
	 */
	public function error(string $title, string $message): void
	{
		$this->client->post('alert/error', ['title' => $title, 'message' => $message]);
	}
}
