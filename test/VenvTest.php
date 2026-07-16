<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Test;

use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use SATHub\PHPPythonVenv\Exception\PipCommandCannotBeExecutedException;
use SATHub\PHPPythonVenv\Exception\VenvDoesNotExistsException;
use SATHub\PHPUnit\Base;

use SATHub\PHPPythonVenv\Venv;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class VenvTest extends Base
{
	use ContentTrait;

	protected const PATH = __DIR__ . '/venv';

	#[BeforeClass]
	public static function clearVenvBeforeTests(): void {
		self::clearSut();
		self::assertDirectoryExists(self::PATH);
		self::assertDirectoryIsWritable(self::PATH);
		self::assertArray(glob(self::PATH . '/*'));
	}

	#[Test]
	public function existsBeforeInitReturnsFalse(): void {
		$this->assertFalse(Venv::exists(self::PATH));
	}

	#[Test]
	#[Depends('existsBeforeInitReturnsFalse')]
	public function constructBeforeInitThrowsException(): void {
		$this->expectException(VenvDoesNotExistsException::class);

		new Venv(self::PATH);
	}

	#[Test]
	#[Depends('constructBeforeInitThrowsException')]
	public function init(): void {
		$this->assertInstanceOf(Venv::class, Venv::init(self::PATH));
	}

	#[Test]
	#[Depends('init')]
	public function exists(): void {
		$this->assertTrue(Venv::exists(self::PATH));
	}

	#[Test]
	#[Depends('init')]
	public function construct(): void {
		new Venv(self::PATH);

		$this->pass();
	}

	#[Test]
	#[Depends('exists')]
	public function use(): Venv {
		$venv = Venv::use(self::PATH);

		$this->assertTrue(Venv::exists(self::PATH));

		return $venv;
	}

	#[Test]
	#[Depends('use')]
	public function python(Venv $venv): void {
		$this->assertSame(0, $venv->python(__DIR__ . '/python/test-stdin.py', $output, self::CONTENT));
		$this->assertIsCorrectContent($output);
	}

	#[Test]
	#[Depends('init')]
	public function pythonUsesVenv(): void {
		$venv = new Venv(self::PATH);

		$this->assertSame(0, $venv->python(__DIR__ . '/python/test-stdin.py', $output, self::CONTENT));
		$this->assertIsCorrectContent($output);
	}

	#[Test]
	#[Depends('use')]
	public function pip(Venv $venv): void {
		$this->assertSame(0, $venv->pip('-V', $output));
		$this->assertArray($output, 1, 'string');
		$this->assertStringStartsWith('pip ', $output[0]);
		$this->assertStringContainsString(' from ' . __DIR__ . '/venv/', $output[0]);

		$this->assertSame(0, $venv->pip(['install', 'pansi']));
	}

	#[Test]
	#[Depends('use')]
	public function pipWithEmptyCommandThrowsException(Venv $venv): void {
		$this->expectException(PipCommandCannotBeExecutedException::class);

		$venv->pip('   ');
	}

	protected static function clearSut(): void {
		exec('rm -rf ' . self::PATH . '/*', $output, $result);
		if ($result !== 0) {
			self::fail(implode('\n', $output));
		}
	}
}
