<?php

namespace Simplefied\Example;

interface HttpClientInterface
{
	public function get(string $url, array $headers = []): HttpResponse;
	public function post(string $url, string $body, array $headers = []): HttpResponse;
	public function put(string $url, string $body, array $headers = []): HttpResponse;
}