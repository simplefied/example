<?php

namespace Simplefied\Example;

class Draft implements \JsonSerializable
{
	private string $name;
	private string $text;

	public static function fromComment(Comment $comment): static
	{
		return new static($comment->getName(), $comment->getText());
	}

	public function __construct(string $name, string $text)
	{
		$this->setName($name);
		$this->setText($text);
	}

	public function setName(string $name): void
	{
		if (empty($name)) {
			throw new \ValueError('Draft name must not be empty');
		}
		$this->name = $name;
	}

	public function setText(string $text): void
	{
		if (empty($text)) {
			throw new \ValueError('Draft text must not be empty');
		}
		$this->text = $text;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function jsonSerialize()
	{
		return [
			'name' => $this->name,
			'text' => $this->text,
		];
	}
}