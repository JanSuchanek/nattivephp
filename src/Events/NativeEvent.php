<?php

declare(strict_types=1);

namespace NativePHP\Nette\Events;

/**
 * Base class for all NativePHP events.
 * Extend this for type-safe event handling.
 */
abstract class NativeEvent
{
	/** @param array<string, mixed> $payload */
	public function __construct(
		public readonly array $payload = [],
	) {
	}
}
