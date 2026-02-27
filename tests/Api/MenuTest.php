<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NativePHP\Nette\Client;
use NativePHP\Nette\Menu;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

class MenuTest extends TestCase
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

	public function testSetSendsItems(): void
	{
		$history = [];
		$client = $this->createClient($history, [new Response(200)]);
		$menu = new Menu($client);

		$menu->set([['label' => 'File', 'submenu' => []]]);

		Assert::count(1, $history);
		Assert::same('/api/menu', $history[0]['request']->getUri()->getPath());
		$body = json_decode((string) $history[0]['request']->getBody(), true);
		Assert::same('File', $body['items'][0]['label']);
	}
}

(new MenuTest)->run();
