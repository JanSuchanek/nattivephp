<?php

declare(strict_types=1);

namespace NativePHP\Nette\Command;

use Nette\Neon\Neon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NativeConfigCommand extends Command
{
	protected static $defaultName = 'native:config';

	public function __construct(
		private readonly string $configFile,
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setDescription('Output NativePHP configuration as JSON');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$config = [];
		if (file_exists($this->configFile)) {
			$neon = Neon::decodeFile($this->configFile);
			$config = $neon['nativephp'] ?? $neon ?? [];
		}

		$output->write(json_encode($config, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

		return Command::SUCCESS;
	}
}
