<?php

namespace Artvys\Search\Result;

use JsonSerializable;

class Tag implements JsonSerializable {
	protected string $title;
	protected string $url;
	protected mixed $color;

	public static function make(string $title, string $url, mixed $color = null): self {
		return new self($title, $url, $color);
	}

	public function __construct(string $title, string $url, mixed $color = null) {
		$this->title = $title;
		$this->url = $url;
		$this->color = $color;
	}

	public function title(): string {
		return $this->title;
	}

	public function setTitle(string $title): static {
		$this->title = $title;
		return $this;
	}

	public function url(): string {
		return $this->url;
	}

	public function setUrl(string $url): static {
		$this->url = $url;
		return $this;
	}

	public function color(): mixed {
		return $this->color;
	}

	public function setColor(mixed $color): static {
		$this->color = $color;
		return $this;
	}

	public function hasColor(): bool {
		return !empty($this->color);
	}

	/** @return array<string, mixed> */
	public function jsonSerialize(): array {
		return [
			'title' => $this->title(),
			'url' => $this->url(),
			'color' => $this->color(),
		];
	}
}
