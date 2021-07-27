<?php

use PHPUnit\Framework\TestCase;

use Simplefied\Example\Comment;
use Simplefied\Example\Draft;

final class DraftTest extends TestCase
{
	public function testCanBeCreatedFromComment(): void
	{
		$comment = Comment::fromAssoc([
			'id' => 3,
			'name' => 'Test Name',
			'text' => 'Test Text',
		]);
		$draft = Draft::fromComment($comment);
		$this->assertEquals('Test Name', $draft->getName());
		$this->assertEquals('Test Text', $draft->getText());
	}

	public function testCanBeModified(): void
	{
		$draft = new Draft('Name', 'Text');
		$draft->setName('Test Name');
		$draft->setText('Test Text');
		$this->assertEquals('Test Name', $draft->getName());
		$this->assertEquals('Test Text', $draft->getText());
	}
}