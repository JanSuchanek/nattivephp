<?php

declare(strict_types=1);

namespace NativePHP\Nette\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Build native desktop app (DMG, exe, AppImage).
 *
 * @see Laravel: php artisan native:build
 */
class NativeBuildCommand extends Command
{
	protected function configure(): void
	{
		$this->setName('native:build')
			->setDescription('Build the NativePHP desktop application')
			->addOption('platform', 'p', InputOption::VALUE_REQUIRED, 'Target platform: mac-arm64, mac-x86, win-x64, linux-x64', 'mac-arm64');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$platform = $input->getOption('platform');
		$projectDir = (string) getcwd();
		$electronDir = $projectDir . '/electron';

		if (!is_dir($electronDir)) {
			$output->writeln('<error>electron/ directory not found. Run native:install first.</error>');
			return Command::FAILURE;
		}

		// Sync files
		$output->writeln('<info>Syncing application files...</info>');
		$targetDir = $electronDir . '/resources/app';
		foreach (['app', 'config', 'bin'] as $dir) {
			$src = $projectDir . '/' . $dir;
			if (is_dir($src)) {
				@mkdir($targetDir . '/' . $dir, 0777, true);
				shell_exec("cp -R {$src}/ {$targetDir}/{$dir}/");
			}
		}
		if (is_dir($projectDir . '/vendor')) {
			shell_exec("rsync -a {$projectDir}/vendor/ {$targetDir}/vendor/ --delete 2>/dev/null");
		}

		// Build
		$output->writeln("<info>Building for {$platform}...</info>");
		$cmd = "cd {$electronDir} && npm run build:{$platform}";
		$output->writeln("<comment>$ {$cmd}</comment>");
		passthru($cmd, $exitCode);

		if ($exitCode === 0) {
			$output->writeln('<info>Build complete! Check electron/dist/ for output.</info>');
		}

		return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
	}
}
