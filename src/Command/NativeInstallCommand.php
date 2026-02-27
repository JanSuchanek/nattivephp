<?php

declare(strict_types=1);

namespace NativePHP\Nette\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Scaffold the Electron directory into a Nette project.
 *
 * @see Laravel: php artisan native:install
 */
class NativeInstallCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('native:install')
			->setDescription('Initialize the NativePHP Electron directory structure');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$projectDir = (string) getcwd();
		$electronDir = $projectDir . '/electron';

		if (is_dir($electronDir)) {
			$output->writeln('<comment>electron/ directory already exists, skipping.</comment>');
			return Command::SUCCESS;
		}

		// Check if template exists in vendor
		$templateDir = $projectDir . '/vendor/jansuchanek/nattivephp/electron-template';
		if (!is_dir($templateDir)) {
			$output->writeln('<error>Electron template not found in vendor package.</error>');
			$output->writeln('<info>Copy the electron/ directory from the nattivephp repository manually.</info>');
			return Command::FAILURE;
		}

		$output->writeln('<info>Creating electron/ directory...</info>');
		shell_exec("cp -R {$templateDir} {$electronDir}");

		$output->writeln('<info>Installing npm dependencies...</info>');
		passthru("cd {$electronDir} && npm install", $exitCode);

		if ($exitCode === 0) {
			$output->writeln('<info>Done! Run `php bin/console native:serve` to start.</info>');
		}

		return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
	}
}
