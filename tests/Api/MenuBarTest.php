<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\MenuBar;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class MenuBarTest extends TestCase
{
	/** @param array<int, array<string, mixed>> $history */
	private function createClient(array &$history, array $responses): Client
	{
		$mock = new MockHandler($responses);
		$stack = HandlerStack::create($mock);
		$stack->push(Middleware::history($history));
		return new Client(new GuzzleClient([
			'base_uri' => 'http://localhost:4000/api/',
			'handler' => $stack,
		]));
	}

	public function testCreate(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		$mb = new MenuBar($client);
		$mb->create(['width' => 400, 'height' => 300, 'tooltip' => 'My App']);
		Assert::same('/api/menu-bar/create', $history[0]['request']->getUri()->getPath());
	}

	public function testLabel(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new MenuBar($client))->label('Hello');
		Assert::same('/api/menu-bar/label', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('Hello', $body['label']);
	}

	public function testTooltip(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new MenuBar($client))->tooltip('Tip');
		Assert::same('/api/menu-bar/tooltip', $history[0]['request']->getUri()->getPath());
	}

	public function testShowHide(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200), new Response(200)]);
		$mb = new MenuBar($client);
		$mb->show();
		$mb->hide();
		Assert::same('/api/menu-bar/show', $history[0]['request']->getUri()->getPath());
		Assert::same('/api/menu-bar/hide', $history[1]['request']->getUri()->getPath());
	}

	public function testResize(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		(new MenuBar($client))->resize(800, 600);
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same(800, $body['width']);
		Assert::same(600, $body['height']);
	}
}

(new MenuBarTest)->run();
