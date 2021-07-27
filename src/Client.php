<?php

namespace Simplefied\Example;

class Client
{
	private HttpClientInterface $httpClient;
	private string $baseUrl = 'http://example.com';

	public function __construct(HttpClientInterface $httpClient = null)
	{
		$this->httpClient = $httpClient ?? new CurlHttpClient();
	}

	public function createComment(Draft $draft): Comment
	{
		$body = json_encode($draft);
		$response = $this->httpClient->post($this->baseUrl . '/comment', $body, [
			'Content-Type' => 'application/json; charset=UTF-8',
		]);
		if ($response->getCode() != 200) {
			throw new \Exception('Example server returned HTTP code ' . $response->getCode());
		}

		$comment_data = $this->getDataFromJson($response->getBody());
		if (!is_array($comment_data)) {
			throw new \ValueError('JSON root element \'data\' must be an array');
		}

		return Comment::fromAssoc($comment_data);
	}

	public function readComments(): array
	{
		$response = $this->httpClient->get($this->baseUrl . '/comments');
		if ($response->getCode() != 200) {
			throw new \Exception('Example server returned HTTP code ' . $response->getCode());
		}
		
		$data = $this->getDataFromJson($response->getBody());
		if (!is_array($data)) {
			throw new \ValueError('JSON root element \'data\' must be an array');
		}

		$comments = [];
		foreach ($data as $comment_data) {
			$comments[] = Comment::fromAssoc($comment_data);
		}
		return $comments;
	}

	public function updateComment(int $id, Draft $draft): Comment
	{
		$body = json_encode($draft);
		$response = $this->httpClient->put($this->baseUrl . '/comment/' . $id, $body);
		if ($response->getCode() != 200) {
			throw new \Exception('Example server returned HTTP code ' . $response->getCode());
		}

		$comment_data = $this->getDataFromJson($response->getBody());
		if (!is_array($comment_data)) {
			throw new \ValueError('JSON root element \'data\' must be an array');
		}

		return Comment::fromAssoc($comment_data);
	}

	private function getDataFromJson(string $json)
	{
		$array = json_decode($json, true);
		if ($array === null) {
			throw new \ValueError('JSON is not parsable');
		}
		if (!is_array($array)) {
			throw new \ValueError('JSON root is not an array');
		}
		if (!array_key_exists('success', $array) || !is_bool($array['success'])) {
			throw new \ValueError('JSON root element \'success\' required and must be a boolean');
		}
		if (!$array['success']) {
			if (!array_key_exists('error', $array) || !is_string($array['error'])) {
				throw new \ValueError('JSON root element \'error\' required and must be a string');
			}
			throw new \ValueError('Example server returned an error: ' . $array['error']);
		}
		if (!array_key_exists('data', $array)) {
			throw new \ValueError('JSON root element \'data\' required');
		}
		return $array['data'];
	}
}