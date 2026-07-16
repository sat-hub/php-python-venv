<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Test;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use SATHub\PHPUnit\Base;

use SATHub\PHPPythonVenv\StdinFile;

class StdinFleTest extends Base
{
	protected const CONTENT = 'I am the content of the file!';

	#[Test]
	public function construct(): StdinFile {
		$file = new StdinFile();

		$this->assertFileExists((string)$file);

		return $file;
	}

	#[Test]
	#[Depends('construct')]
	public function write(StdinFile $file): void {
		$this->assertSame($file, $file->write(self::CONTENT));
		$this->assertSame(self::CONTENT, file_get_contents((string)$file));
	}

	#[Test]
	public function withContent(): void {
		$file = StdinFile::withContent(self::CONTENT);

		$this->assertFileExists((string)$file);
		$this->assertSame(self::CONTENT, file_get_contents((string)$file));
	}
}
