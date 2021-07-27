<?php

namespace tests;

use Simplefied\Example\HttpClientInterface;
use Simplefied\Example\HttpResponse;

class FakeHttpClient implements HttpClientInterface
{
	public bool $failing;
	public ?\Closure $cb;

	public function __construct(bool $failing = false, ?\Closure $cb = null)
	{
		$this->failing = $failing;
		$this->cb = $cb;
	}

	private function getResponseFromFile($code, $file)
	{
		return new HttpResponse($code, file_get_contents(dirname(__FILE__) . '/data/' . $file));
	}

	public function get(string $url, array $headers = []): HttpResponse
	{
		if ($this->cb !== null) ($this->cb)($url, null);
		if (!$this->failing) {
			return $this->getResponseFromFile(200, 'list-response.json');
		} else {
			return $this->getResponseFromFile(500, 'fail-response.json');
		}
	}

	public function post(string $url, string $body, array $headers = []): HttpResponse
	{
		if ($this->cb !== null) ($this->cb)($url, $body);
		if (!$this->failing) {
			return $this->getResponseFromFile(200, 'save-response.json');
		} else {
			return $this->getResponseFromFile(500, 'fail-response.json');
		}
	}

	public function put(string $url, string $body, array $headers = []): HttpResponse
	{
		if ($this->cb !== null) ($this->cb)($url, $body);
		if (!$this->failing) {
			return $this->getResponseFromFile(200, 'save-response.json');
		} else {
			return $this->getResponseFromFile(500, 'fail-response.json');
		}
	}
}