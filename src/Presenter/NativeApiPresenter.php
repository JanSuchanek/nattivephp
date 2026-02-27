<?php

declare(strict_types=1);

namespace NativePHP\Nette\Presenter;

use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use NativePHP\Nette\Events\EventDispatcher;
use NativePHP\Nette\NativeAppProvider;

/**
 * Handles Electron → PHP communication endpoints.
 * Register routes: /_native/api/<action> → NativeApi:<action>
 */
class NativeApiPresenter extends Presenter
{
	public function __construct(
		private readonly NativeAppProvider $provider,
		private readonly EventDispatcher $eventDispatcher,
	) {
		parent::__construct();
	}

	public function startup(): void
	{
		parent::startup();

		$secret = $this->getHttpRequest()->getHeader('X-NativePHP-Secret');
		$expected = (string) getenv('NATIVEPHP_SECRET');

		$action = $this->getAction();
		if ($expected !== '' && $action !== 'cookie' && $secret !== $expected) {
			$this->getHttpResponse()->setCode(IResponse::S403_Forbidden);
			$this->sendJson(['error' => 'Invalid secret']);
		}
	}

	public function actionBooted(): void
	{
		$this->provider->boot();
		$this->sendJson(['success' => true]);
	}

	public function actionEvents(): void
	{
		$rawBody = $this->getHttpRequest()->getRawBody();
		/** @var array{event?: string, payload?: array<string, mixed>} $body */
		$body = json_decode($rawBody ?? '{}', true, 512, JSON_THROW_ON_ERROR);

		$event = $body['event'] ?? null;
		$payload = $body['payload'] ?? [];

		if ($event !== null) {
			$this->eventDispatcher->dispatch($event, $payload);
		}

		$this->sendJson(['success' => true]);
	}

	public function actionCookie(): void
	{
		$secret = (string) getenv('NATIVEPHP_SECRET');
		$this->getHttpResponse()->setCookie('_php_native', $secret, '365 days');
		$this->sendJson(['success' => true]);
	}
}
