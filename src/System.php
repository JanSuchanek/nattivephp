<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * System-level features: Touch ID, encryption, printing, themes.
 *
 * @see Electron: electron-plugin/src/server/api/system.ts
 * @see Laravel: NativePHP\Laravel\Facades\System
 * @api GET|POST /api/system/{can-prompt-touch-id,prompt-touch-id,can-encrypt,encrypt,decrypt,printers,print,print-to-pdf,theme}
 */
class System
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	public function canPromptTouchID(): bool
	{
		$result = $this->client->get('system/can-prompt-touch-id');

		return isset($result['result']) && $result['result'] === true;
	}

	/**
	 * Prompt Touch ID authentication.
	 *
	 * @throws \RuntimeException If authentication fails
	 */
	public function promptTouchID(string $reason): void
	{
		$this->client->post('system/prompt-touch-id', ['reason' => $reason]);
	}

	public function canEncrypt(): bool
	{
		$result = $this->client->get('system/can-encrypt');

		return isset($result['result']) && $result['result'] === true;
	}

	public function encrypt(string $string): string
	{
		$result = $this->client->post('system/encrypt', ['string' => $string]);

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : '';
	}

	public function decrypt(string $string): string
	{
		$result = $this->client->post('system/decrypt', ['string' => $string]);

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : '';
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function getPrinters(): array
	{
		$result = $this->client->get('system/printers');

		return isset($result['printers']) && is_array($result['printers']) ? $result['printers'] : [];
	}

	/**
	 * Print HTML content to a printer.
	 *
	 * @param array<string, mixed> $settings
	 */
	public function print(string $printer, string $html, array $settings = []): void
	{
		$this->client->post('system/print', ['printer' => $printer, 'html' => $html, 'settings' => $settings]);
	}

	/**
	 * Print HTML content to PDF.
	 *
	 * @param array<string, mixed> $settings
	 */
	public function printToPdf(string $html, array $settings = []): string
	{
		$result = $this->client->post('system/print-to-pdf', ['html' => $html, 'settings' => $settings]);

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : '';
	}

	/**
	 * Get current theme ('system', 'light', 'dark').
	 */
	public function getTheme(): string
	{
		$result = $this->client->get('system/theme');

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : 'system';
	}

	/**
	 * Set the theme ('system', 'light', 'dark').
	 */
	public function setTheme(string $theme): void
	{
		$this->client->post('system/theme', ['theme' => $theme]);
	}
}
