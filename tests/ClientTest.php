<?php

use PHPUnit\Framework\TestCase;

use tests\FakeHttpClient;

use Simplefied\Example\Comment;
use Simplefied\Example\Client;
use Simplefied\Example\Draft;

final class ClientTest extends TestCase
{
	public function testParsesCommentsRight(): void
	{
		$httpClient = new FakeHttpClient(false, function ($url, $body) {
			$this->assertEquals('http://example.com/comments', $url);
		});
		$client = new Client($httpClient);
		$comments = $client->readComments();
		$this->assertCount(2, $comments);
		$this->assertInstanceOf(Comment::class, $comments[0]);
		$this->assertEquals(1, $comments[0]->getId());
		$this->assertEquals('Ivan Ivanov', $comments[0]->getName());
		$this->assertEquals('Lorem ipsum', $comments[0]->getText());
	}

	public function testThrowsOnGetCommentsError(): void
	{
		$this->expectException(Exception::class);
		$client = new Client(new FakeHttpClient(true));
		$comments = $client->readComments();
	}

	public function testSavesCommentRight(): void
	{
		$httpClient = new FakeHttpClient(false, function ($url, $body) {
			$this->assertEquals('http://example.com/comment', $url);
			$req = json_decode($body, true);
			$this->assertNotEquals(null, $req);
			$this->assertIsArray($req);
			$this->assertArrayHasKey('name', $req);
			$this->assertEquals('Test Name', $req['name']);
			$this->assertArrayHasKey('text', $req);
			$this->assertEquals('Test Text', $req['text']);
			$this->assertArrayNotHasKey('id', $req);
		});
		$client = new Client($httpClient);
		$comment = $client->createComment(new Draft('Test Name', 'Test Text'));
		$this->assertEquals(3, $comment->getId());
		$this->assertEquals('Test Name', $comment->getName());
		$this->assertEquals('Test Text', $comment->getText());
	}

	public function testThrowsOnSaveCommentError(): void
	{
		$this->expectException(Exception::class);
		$client = new Client(new FakeHttpClient(true));
		$comment = $client->createComment(new Draft('Test Name', 'Test Text'));
	}

	public function testUpdatesCommentRight(): void
	{
		$httpClient = new FakeHttpClient(false, function ($url, $body) {
			$this->assertEquals('http://example.com/comment/3', $url);
			$req = json_decode($body, true);
			$this->assertNotEquals(null, $req);
			$this->assertIsArray($req);
			$this->assertArrayHasKey('name', $req);
			$this->assertEquals('Test Name', $req['name']);
			$this->assertArrayHasKey('text', $req);
			$this->assertEquals('Test Text', $req['text']);
		});
		$client = new Client($httpClient);
		$comment = $client->updateComment(3, new Draft('Test Name', 'Test Text'));
		$this->assertEquals(3, $comment->getId());
		$this->assertEquals('Test Name', $comment->getName());
		$this->assertEquals('Test Text', $comment->getText());
	}

	public function testThrowsOnUpdateCommentError(): void
	{
		$this->expectException(Exception::class);
		$client = new Client(new FakeHttpClient(true));
		$comment = $client->updateComment(3, new Draft('Test Name', 'Test Text'));
	}
}