<?php

declare(strict_types=1);

namespace NativePHP\Nette;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * HTTP client for communication with Electron Express API.
 * PHP → Electron direction.
 */
class Client
{
	private readonly GuzzleClient $http;

	public function __construct(string $apiUrl, string $secret, ?GuzzleClient $httpClient = null)
	{
		$this->http = $httpClient ?? new GuzzleClient([
			'base_uri' => rtrim($apiUrl, '/') . '/',
			'timeout' => 30,
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'X-NativePHP-Secret' => $secret,
			],
		]);
	}

	/**
	 * @param array<string, mixed> $query
	 * @return array<string, mixed>
	 * @throws GuzzleException
	 */
	public function get(string $endpoint, array $query = []): array
	{
		$response = $this->http->get($endpoint, ['query' => $query]);
		$body = (string) $response->getBody();

		if ($body === '') {
			return [];
		}

		/** @var array<string, mixed> $decoded */
		$decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

		return $decoded;
	}

	/**
	 * @param array<string, mixed> $data
	 * @throws GuzzleException
	 */
	public function post(string $endpoint, array $data = []): void
	{
		$this->http->post($endpoint, ['json' => $data]);
	}

	/**
	 * @param array<string, mixed> $data
	 * @throws GuzzleException
	 */
	public function delete(string $endpoint, array $data = []): void
	{
		$this->http->delete($endpoint, ['json' => $data]);
	}
}
