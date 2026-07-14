<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv;

use SATHub\PHPPythonVenv\Exception\PHPPythonVenvException;

class StdinFile implements \Stringable
{
	protected string $tmpPath;

	public static function withContent(string $content): static {
		$file = new self();
		return $file->write($content);
	}

	public function __construct() {
		$path = tempnam(sys_get_temp_dir(), 'ppyvenv.stdin_');
		if (!$path) {
			throw new PHPPythonVenvException('Could not create a tmp file.');
		}
		$this->tmpPath = $path;
	}

	public function __destruct() {
		@unlink($this->tmpPath);
	}

	public function write(string $content): static {
		if (file_put_contents($this->tmpPath, $content) !== strlen($content)) {
			throw new PHPPythonVenvException('Could not write content to tmp file.');
		}
		return $this;
	}

	public function __toString(): string {
		return $this->tmpPath;
	}
}
