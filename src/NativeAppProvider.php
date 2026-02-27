<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Boot configuration for the native app.
 * Override this class to customize window setup, menus, etc.
 */
class NativeAppProvider
{
	public function __construct(
		protected Window $window,
	) {
	}

	/**
	 * Called when the native application has been booted.
	 * Open windows, register shortcuts, etc.
	 */
	public function boot(): void
	{
		$phpPort = (string) getenv('NATIVEPHP_PHP_PORT') ?: '8000';

		$this->window->open(
			id: 'main',
			url: "http://127.0.0.1:{$phpPort}/",
			width: 900,
			height: 650,
			title: 'NativePHP Nette App',
		);
	}
}
