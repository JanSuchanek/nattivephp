<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
	->withPaths([__DIR__ . '/src'])
	->withPhpSets(php83: true)
	->withPreparedSets(
		deadCode: true,
		codeQuality: true,
		typeDeclarations: true,
		earlyReturn: true,
	);
