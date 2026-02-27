<?php

declare(strict_types=1);

namespace NativePHP\Nette\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NativePhpIniCommand extends Command
{
	protected static string $defaultName = 'native:php-ini';

	protected function configure(): void
	{
		$this->setDescription('Output PHP INI settings as JSON');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$settings = [
			'memory_limit' => '512M',
			'max_execution_time' => '0',
			'display_errors' => 'Off',
		];

		$output->write(json_encode($settings, JSON_THROW_ON_ERROR));

		return Command::SUCCESS;
	}
}
