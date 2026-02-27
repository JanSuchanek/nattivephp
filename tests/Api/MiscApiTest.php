<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\ProgressBar;
use NativePHP\Nette\PowerMonitor;
use NativePHP\Nette\AutoUpdater;
use NativePHP\Nette\Broadcasting;
use NativePHP\Nette\Debug;
use NativePHP\Nette\ContextMenu;
use NativePHP\Nette\Settings;
use NativePHP\Nette\Alert;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class MiscApiTest extends TestCase
{
	private function createClient(array &$history, array $responses): Client
	{
		$mock = new MockHandler($responses);
		$stack = HandlerStack::create($mock);
		$stack->push(Middleware::history($history));
		return new Client(new GuzzleClient(['base_uri' => 'http://localhost:4000/api/', 'handler' => $stack]));
	}

	// ProgressBar
	public function testProgressBarUpdate(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new ProgressBar($client))->update(0.5);
		Assert::same('/api/progress-bar/update', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same(0.5, $body['percent']);
	}

	// PowerMonitor
	public function testPowerMonitorIdleState(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":"active"}')]);
		Assert::same('active', (new PowerMonitor($client))->getSystemIdleState());
	}

	public function testPowerMonitorIdleTime(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":120}')]);
		Assert::same(120, (new PowerMonitor($client))->getSystemIdleTime());
	}

	public function testPowerMonitorBattery(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":true}')]);
		Assert::true((new PowerMonitor($client))->isOnBatteryPower());
	}

	public function testPowerMonitorThermal(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":"nominal"}')]);
		Assert::same('nominal', (new PowerMonitor($client))->getCurrentThermalState());
	}

	// AutoUpdater
	public function testAutoUpdaterCheck(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new AutoUpdater($client))->checkForUpdates();
		Assert::same('/api/auto-updater/check-for-updates', $history[0]['request']->getUri()->getPath());
	}

	public function testAutoUpdaterDownload(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new AutoUpdater($client))->downloadUpdate();
		Assert::same('/api/auto-updater/download-update', $history[0]['request']->getUri()->getPath());
	}

	public function testAutoUpdaterQuitAndInstall(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new AutoUpdater($client))->quitAndInstall();
		Assert::same('/api/auto-updater/quit-and-install', $history[0]['request']->getUri()->getPath());
	}

	// Broadcasting
	public function testBroadcastSend(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Broadcasting($client))->send('my-event', ['key' => 'value']);
		Assert::same('/api/broadcast', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('my-event', $body['event']);
		Assert::same('value', $body['payload']['key']);
	}

	// Debug
	public function testDebugLog(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Debug($client))->info('test message', ['ctx' => 1]);
		Assert::same('/api/debug/log', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('info', $body['level']);
		Assert::same('test message', $body['message']);
	}

	// ContextMenu
	public function testContextMenuSet(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new ContextMenu($client))->set([['label' => 'Copy']]);
		Assert::same('/api/context', $history[0]['request']->getUri()->getPath());
		Assert::same('POST', $history[0]['request']->getMethod());
	}

	public function testContextMenuRemove(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new ContextMenu($client))->remove();
		Assert::same('DELETE', $history[0]['request']->getMethod());
	}

	// Settings
	public function testSettingsGetSet(): void
	{
		$history = [];
		$client = $this->createClient($history, [
			new Response(200, [], '{"value":"bar"}'),
			new Response(200),
		]);
		$s = new Settings($client);
		Assert::same('bar', $s->get('foo'));
		$s->set('foo', 'baz');
		Assert::same('/api/settings/foo', $history[1]['request']->getUri()->getPath());
	}

	public function testSettingsDelete(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Settings($client))->delete('key');
		Assert::same('DELETE', $history[0]['request']->getMethod());
		Assert::same('/api/settings/key', $history[0]['request']->getUri()->getPath());
	}

	// Alert
	public function testAlertMessage(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":1}')]);
		$result = (new Alert($client))->message('Are you sure?', 'question', 'Confirm');
		Assert::same(1, $result);
		Assert::same('/api/alert/message', $history[0]['request']->getUri()->getPath());
	}

	public function testAlertError(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Alert($client))->error('Error', 'Something failed');
		Assert::same('/api/alert/error', $history[0]['request']->getUri()->getPath());
	}
}

(new MiscApiTest)->run();
