<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\System;
use NativePHP\Nette\ChildProcess;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class SystemChildProcessTest extends TestCase
{
	private function createClient(array &$history, array $responses): Client
	{
		$mock = new MockHandler($responses);
		$stack = HandlerStack::create($mock);
		$stack->push(Middleware::history($history));
		return new Client(new GuzzleClient(['base_uri' => 'http://localhost:4000/api/', 'handler' => $stack]));
	}

	// System
	public function testCanPromptTouchID(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":true}')]);
		Assert::true((new System($client))->canPromptTouchID());
	}

	public function testEncryptDecrypt(): void
	{
		$history = [];
		$client = $this->createClient($history, [
			new Response(200, [], '{"result":"encrypted_base64"}'),
			new Response(200, [], '{"result":"decrypted_text"}'),
		]);
		$sys = new System($client);
		Assert::same('encrypted_base64', $sys->encrypt('secret'));
		Assert::same('decrypted_text', $sys->decrypt('encrypted_base64'));
	}

	public function testGetTheme(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"result":"dark"}')]);
		Assert::same('dark', (new System($client))->getTheme());
	}

	public function testSetTheme(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new System($client))->setTheme('light');
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('light', $body['theme']);
	}

	public function testGetPrinters(): void
	{
		$history = [];
		$client = $this->createClient($history, [
			new Response(200, [], '{"printers":[{"name":"HP"}]}'),
		]);
		$printers = (new System($client))->getPrinters();
		Assert::count(1, $printers);
		Assert::same('HP', $printers[0]['name']);
	}

	// ChildProcess
	public function testStart(): void
	{
		$history = [];
		$client = $this->createClient($history, [
			new Response(200, [], '{"alias":"worker","pid":1234}'),
		]);
		$result = (new ChildProcess($client))->start(['alias' => 'worker', 'cmd' => ['node', 'script.js']]);
		Assert::same('/api/child-process/start', $history[0]['request']->getUri()->getPath());
		Assert::same('worker', $result['alias']);
	}

	public function testStop(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new ChildProcess($client))->stop('worker');
		Assert::same('/api/child-process/stop', $history[0]['request']->getUri()->getPath());
	}

	public function testMessage(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new ChildProcess($client))->message('worker', ['type' => 'ping']);
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('worker', $body['alias']);
		Assert::same('ping', $body['message']['type']);
	}
}

(new SystemChildProcessTest)->run();
