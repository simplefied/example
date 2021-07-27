<?php

namespace Simplefied\Example;

class Comment
{
	private int    $id;
	private string $name;
	private string $text;

	public static function fromAssoc(array $array): static
	{
		if (!array_key_exists('name', $array) || !is_string($array['name'])) {
			throw new \ValueError('Comment\'s data element \'name\' required and must be a string');
		}
		if (!array_key_exists('text', $array) || !is_string($array['text'])) {
			throw new \ValueError('Comment\'s data element \'text\' required and must be a string');
		}
		if (!array_key_exists('id', $array) || !is_int($array['id'])) {
			throw new \ValueError('Comment\'s data element \'id\' required and must be an integer');
		}
		
		return new static($array['id'], $array['name'], $array['text']);
	}

	private function __construct(int $id, string $name, string $text)
	{
		$this->id   = $id;
		$this->name = $name;
		$this->text = $text;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getText(): string
	{
		return $this->text;
	}
}