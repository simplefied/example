<?php

namespace Simplefied\Example;

class CurlHttpClient implements HttpClientInterface
{
	public function getDefaultOptions(): array
	{
		return [
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_RETURNTRANSFER => true,

			// SSL config
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,

			// Redirects
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS      => 10,
		];
	}

	public function request(string $url, array $headers = [], array $curl_options = []): HttpResponse
	{
		$ch = curl_init($url);

		curl_setopt_array($ch, $this->getDefaultOptions());

		$raw_headers = [];
		foreach ($headers as $header => $value) {
			$raw_headers[] = "$header: $value";
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $raw_headers);

		curl_setopt_array($ch, $curl_options);

		$response = curl_exec($ch);
		$curlinfo = curl_getinfo($ch);
		$last_err = curl_error($ch);

		curl_close($ch);

		if ($response === false) {
			throw new \Exception('cURL error: ' . $last_err);
		}

		return new HttpReponse($curlinfo['http_code'], $response);
	}

	public function get(string $url, array $headers = []): HttpResponse
	{
		return $this->request($url, $headers);
	}

	public function post(string $url, string $body, array $headers = []): HttpResponse
	{
		return $this->request($url, $headers, [
			CURLOPT_POST       => true,
			CURLOPT_POSTFIELDS => $body,
		]);
	}

	public function put(string $url, string $body, array $headers = []): HttpResponse
	{
		return $this->request($url, $headers, [
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_POSTFIELDS    => $body,
		]);
	}
}