<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Test;

trait ContentTrait
{
	protected const CONTENT = 'These are 24 characters.';

	/**
	 * @param array<string>|null $output
	 */
	protected static function assertIsCorrectContent(?array $output): void {
		self::assertArray($output, 1, 'string');
		self::assertSame(strlen(self::CONTENT) . ' ' . self::CONTENT, $output[0]);
	}
}
