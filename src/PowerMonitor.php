<?php

declare(strict_types=1);

namespace NativePHP\Nette;

/**
 * Power state monitoring (battery, idle, thermal).
 *
 * @see Electron: electron-plugin/src/server/api/powerMonitor.ts
 * @see Laravel: NativePHP\Laravel\Facades\PowerMonitor
 * @api GET /api/power-monitor/{get-system-idle-state,get-system-idle-time,get-current-thermal-state,is-on-battery-power}
 */
class PowerMonitor
{
	public function __construct(
		private readonly Client $client,
	) {
	}

	/**
	 * Get system idle state ('active', 'idle', 'locked', 'unknown').
	 */
	public function getSystemIdleState(int $threshold = 60): string
	{
		$result = $this->client->get("power-monitor/get-system-idle-state?threshold={$threshold}");

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : 'unknown';
	}

	/**
	 * Get the system idle time in seconds.
	 */
	public function getSystemIdleTime(): int
	{
		$result = $this->client->get('power-monitor/get-system-idle-time');

		return isset($result['result']) && is_int($result['result']) ? $result['result'] : 0;
	}

	/**
	 * Get thermal state ('unknown', 'nominal', 'fair', 'serious', 'critical').
	 */
	public function getCurrentThermalState(): string
	{
		$result = $this->client->get('power-monitor/get-current-thermal-state');

		return isset($result['result']) && is_string($result['result']) ? $result['result'] : 'unknown';
	}

	/**
	 * Check if the system is running on battery power.
	 */
	public function isOnBatteryPower(): bool
	{
		$result = $this->client->get('power-monitor/is-on-battery-power');

		return isset($result['result']) && $result['result'] === true;
	}
}
