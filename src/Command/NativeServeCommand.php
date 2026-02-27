<?php

declare(strict_types=1);

namespace NativePHP\Nette\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Start the Electron dev server with PHP.
 *
 * @see Laravel: php artisan native:serve
 */
class NativeServeCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('native:serve')
			->setDescription('Start the NativePHP Electron development server')
			->addOption('no-reload', null, InputOption::VALUE_NONE, 'Disable auto-reload on file changes');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$projectDir = (string) getcwd();
		$electronDir = $projectDir . '/electron';

		if (!is_dir($electronDir)) {
			$output->writeln('<error>electron/ directory not found. Run native:install first.</error>');
			return Command::FAILURE;
		}

		// Sync app files to electron/resources/app/
		$output->writeln('<info>Syncing application files...</info>');
		$this->syncFiles($projectDir, $electronDir);

		// Start Electron dev server
		$output->writeln('<info>Starting Electron dev server...</info>');
		$cmd = "cd {$electronDir} && npm run dev";

		$output->writeln("<comment>$ {$cmd}</comment>");
		passthru($cmd, $exitCode);

		return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
	}

	private function syncFiles(string $projectDir, string $electronDir): void
	{
		$targetDir = $electronDir . '/resources/app';

		$dirs = ['app', 'config', 'bin'];
		foreach ($dirs as $dir) {
			$src = $projectDir . '/' . $dir;
			$dst = $targetDir . '/' . $dir;
			if (is_dir($src)) {
				@mkdir($dst, 0777, true);
				shell_exec("cp -R {$src}/ {$dst}/");
			}
		}

		// Sync vendor
		if (is_dir($projectDir . '/vendor')) {
			shell_exec("rsync -a {$projectDir}/vendor/ {$targetDir}/vendor/ --delete 2>/dev/null");
		}
	}
}
