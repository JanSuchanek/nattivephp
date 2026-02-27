<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\Dock;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class DockTest extends TestCase
{
	private function createClient(array &$history, array $responses): Client
	{
		$mock = new MockHandler($responses);
		$stack = HandlerStack::create($mock);
		$stack->push(Middleware::history($history));
		return new Client(new GuzzleClient(['base_uri' => 'http://localhost:4000/api/', 'handler' => $stack]));
	}

	public function testShowHide(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200), new Response(200)]);
		$dock = new Dock($client);
		$dock->show();
		$dock->hide();
		Assert::same('/api/dock/show', $history[0]['request']->getUri()->getPath());
		Assert::same('/api/dock/hide', $history[1]['request']->getUri()->getPath());
	}

	public function testBounce(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Dock($client))->bounce('critical');
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('critical', $body['type']);
	}

	public function testSetBadge(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Dock($client))->setBadge('5');
		Assert::same('/api/dock/badge', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('5', $body['label']);
	}

	public function testGetBadge(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200, [], '{"label":"3"}')]);
		Assert::same('3', (new Dock($client))->getBadge());
	}

	public function testIcon(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new Dock($client))->icon('/path/to/icon.png');
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('/path/to/icon.png', $body['path']);
	}
}

(new DockTest)->run();
