<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\GlobalShortcut;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class GlobalShortcutTest extends TestCase
{
	private function createClient(array &$history, array $responses): Client
	{
		$mock = new MockHandler($responses);
		$stack = HandlerStack::create($mock);
		$stack->push(Middleware::history($history));
		return new Client(new GuzzleClient(['base_uri' => 'http://localhost:4000/api/', 'handler' => $stack]));
	}

	public function testRegister(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new GlobalShortcut($client))->register('CmdOrCtrl+X', 'App\\Events\\CutEvent');
		Assert::same('/api/global-shortcuts', $history[0]['request']->getUri()->getPath());
		Assert::same('POST', $history[0]['request']->getMethod());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('CmdOrCtrl+X', $body['key']);
	}

	public function testUnregister(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new GlobalShortcut($client))->unregister('CmdOrCtrl+X');
		Assert::same('DELETE', $history[0]['request']->getMethod());
	}

	public function testIsRegistered(): void
	{
		$history = [];
		$client = $this->createClient($history, [
			new Response(200, [], '{"isRegistered":true}'),
		]);
		Assert::true((new GlobalShortcut($client))->isRegistered('CmdOrCtrl+X'));
	}
}

(new GlobalShortcutTest)->run();
