<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

class Lexer implements CompilerInput {
	private readonly TokenCollector $collector;
	private string $lexedQuery = '';

	public function __construct(TokenCollector $collector) {
		$this->collector = $collector;
	}

	public function process(string $query): void {
		$this->lexedQuery = $query;

		while ($this->lexedQuery !== '')
			$this->lex();

		$this->collector->eof();
	}

	private function lex(): void {
		(
			$this->skipWhitespace()
			|| $this->lexReservedSymbols()
			|| $this->lexSymbol()
			|| $this->lexToken()
			|| $this->failsafeSkip()
		);
	}

	private function skipWhitespace(): bool {
		if ($matches = $this->match('/^\s+(.*)/')) {
			$this->lexedQuery = $matches[1];
			return true;
		}

		return false;
	}

	private function lexReservedSymbols(): bool {
		$symbol = $this->lexedQuery[0] ?? '';
		$map = [
			',' => $this->collector->comma(...),
			':' => $this->collector->colon(...),
		];

		if ($action = $map[$symbol] ?? false) {
			$action($symbol);
			$this->advance();
			return true;
		}

		return false;
	}

	private function lexSymbol(): bool {
		$symbol = $this->lexedQuery[0] ?? '';

		if (in_array($symbol, $this->symbols())) {
			$this->collector->symbol($symbol);
			$this->advance();
			return true;
		}

		return false;
	}

	/** @return string[] */
	private function symbols(): array {
		return ['!', '#', '$', '%', '&', '*', '+', '-', '.', '/', ';', '<', '=', '>', '?', '@', '\\', '^', '_', '|', '~'];
	}

	private function lexToken(): bool {
		if ($matches = $this->match('/^([^\s,:]+)(.*)/')) {
			$this->collector->token($matches[1]);
			$this->lexedQuery = $matches[2];
			return true;
		}

		return false;
	}

	/**
	 * This method should never be called. It serves as failsafe to prevent infinite loop.
	 * @return bool
	 */
	protected function failsafeSkip(): bool {
		$this->advance();
		return true;
	}

	/**
	 * @param string $pattern
	 * @return string[]|false
	 */
	private function match(string $pattern): array|false {
		$matches = [];

		if (!preg_match($pattern, $this->lexedQuery, $matches))
			return false;

		return $matches;
	}

	private function advance(): void {
		$this->lexedQuery = mb_substr($this->lexedQuery, 1);
	}
}
