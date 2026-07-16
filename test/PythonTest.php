<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Test;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use SATHub\PHPUnit\Base;

use SATHub\PHPPythonVenv\Exception\PythonCommandCannotBeExecutedException;
use SATHub\PHPPythonVenv\Exception\PythonInterpreterNotFoundException;
use SATHub\PHPPythonVenv\Python;

class PythonTest extends Base
{
	use ContentTrait;

	#[Test]
	public function construct(): Python {
		$python = new Python();

		$this->pass();

		return $python;
	}

	#[Test]
	#[Depends('construct')]
	public function getVersion(Python $python): void {
		$this->assertSame(1, preg_match('/^3\.[0-9]+\.[0-9]+/', $python->getVersion()));
	}

	#[Test]
	public function constructThrowsExceptionIfCustomExecutableIsNotFound(): void {
		$this->expectException(PythonInterpreterNotFoundException::class);

		Python::setCustomExecutable('/usr/bin/python-not-here-i-am-sorry');
	}

	#[Test]
	#[Depends('construct')]
	public function runPython(Python $python): void {
		$this->assertSame(0, $python->run('-V', $output));
		$this->assertArray($output, 1, 'string');
		$this->assertStringStartsWith('Python ', $output[0]);
		$this->assertStringContainsString($python->getVersion(), $output[0]);
	}

	#[Test]
	#[Depends('construct')]
	public function runPythonCommandWithError(Python $python): void {
		$this->assertGreaterThan(0, $python->run(['--ThisIsAnOptionThatDoesNotExist', '2>&1']));
	}

	#[Test]
	#[Depends('construct')]
	public function runEmptyPythonCommandThrowsException(Python $python): void {
		$this->expectException(PythonCommandCannotBeExecutedException::class);

		$python->run('  ');
	}

	#[Test]
	#[Depends('construct')]
	public function runPythonWithInputAndOutput(Python $python): void {
		$this->assertSame(0, $python->run(__DIR__ . '/python/test-stdin.py', $output, self::CONTENT));
		$this->assertIsCorrectContent($output);
	}
}
