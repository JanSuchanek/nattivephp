<?php

declare(strict_types=1);

namespace NativePHP\Nette;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Nette DI extension for NativePHP bridge.
 * Registers all API services into the container.
 */
class NativePhpExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'app_id' => Expect::string('com.nativephp.app'),
			'app_name' => Expect::string('NativePHP'),
			'version' => Expect::string('1.0.0'),
			'author' => Expect::string(''),
			'deeplink_scheme' => Expect::string(''),
			'provider' => Expect::string(NativeAppProvider::class),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var \stdClass $config */
		$config = $this->getConfig();

		// Client reads NATIVEPHP_API_URL and NATIVEPHP_SECRET from env at runtime
		$builder->addDefinition($this->prefix('client'))
			->setFactory(Client::class);

		$builder->addDefinition($this->prefix('window'))
			->setFactory(Window::class);

		$builder->addDefinition($this->prefix('clipboard'))
			->setFactory(Clipboard::class);

		$builder->addDefinition($this->prefix('notification'))
			->setFactory(Notification::class);

		$builder->addDefinition($this->prefix('shell'))
			->setFactory(Shell::class);

		$builder->addDefinition($this->prefix('dialog'))
			->setFactory(Dialog::class);

		$builder->addDefinition($this->prefix('screen'))
			->setFactory(Screen::class);

		$builder->addDefinition($this->prefix('app'))
			->setFactory(App::class);

		// New API classes
		$builder->addDefinition($this->prefix('menu'))
			->setFactory(Menu::class);

		$builder->addDefinition($this->prefix('menuBar'))
			->setFactory(MenuBar::class);

		$builder->addDefinition($this->prefix('globalShortcut'))
			->setFactory(GlobalShortcut::class);

		$builder->addDefinition($this->prefix('dock'))
			->setFactory(Dock::class);

		$builder->addDefinition($this->prefix('progressBar'))
			->setFactory(ProgressBar::class);

		$builder->addDefinition($this->prefix('powerMonitor'))
			->setFactory(PowerMonitor::class);

		$builder->addDefinition($this->prefix('childProcess'))
			->setFactory(ChildProcess::class);

		$builder->addDefinition($this->prefix('autoUpdater'))
			->setFactory(AutoUpdater::class);

		$builder->addDefinition($this->prefix('contextMenu'))
			->setFactory(ContextMenu::class);

		$builder->addDefinition($this->prefix('system'))
			->setFactory(System::class);

		$builder->addDefinition($this->prefix('settings'))
			->setFactory(Settings::class);

		$builder->addDefinition($this->prefix('broadcasting'))
			->setFactory(Broadcasting::class);

		$builder->addDefinition($this->prefix('alert'))
			->setFactory(Alert::class);

		$builder->addDefinition($this->prefix('debug'))
			->setFactory(Debug::class);

		$provider = is_string($config->provider) ? $config->provider : NativeAppProvider::class;

		$builder->addDefinition($this->prefix('provider'))
			->setFactory($provider);
	}
}
