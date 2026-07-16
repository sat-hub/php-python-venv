<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv;

use SATHub\PHPPythonVenv\Exception\PHPPythonVenvException;
use SATHub\PHPPythonVenv\Exception\PipCommandCannotBeExecutedException;
use SATHub\PHPPythonVenv\Exception\VenvDoesNotExistsException;

class Venv
{
	protected string $venv;

	protected ?Python $python = null;

	public static function exists(string $pathToVenv): bool {
		$path = realpath(trim($pathToVenv));
		if ($path && is_dir($path)) {
			if (is_file($path . DIRECTORY_SEPARATOR . 'pyvenv.cfg')) {
				$bin = $path . DIRECTORY_SEPARATOR . 'bin';
				if (is_dir($bin)) {
					$python = $bin . DIRECTORY_SEPARATOR . 'python';
					if (is_file($python) && is_executable($python)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public static function init(string $pathToVenv): self {
		if (!self::exists($pathToVenv)) {
			$pathToVenv = trim($pathToVenv);
			$python     = new Python();
			if ($python->run('-m venv ' . $pathToVenv, $output) !== 0) {
				throw new PHPPythonVenvException(implode('\n', $output));
			}
		}
		return self::use($pathToVenv);
	}

	public static function use(string $pathToVenv): self {
		$venv = new self($pathToVenv);
		Python::setCustomExecutable($venv->getExecutable());
		$venv->python = new Python();
		return $venv;
	}

	public function __construct(string $pathToVenv) {
		if (!self::exists($pathToVenv)) {
			throw new VenvDoesNotExistsException($pathToVenv);
		}
		$this->venv = realpath(trim($pathToVenv));
	}

	/**
	 * @param array<string>|string $arguments
	 * @param array<string>|null $output
	 */
	public function python(array|string $arguments, ?array &$output = null, ?string $input = null): int {
		if (!$this->python) {
			Python::setCustomExecutable($this->getExecutable());
			$this->python = new Python();
		}
		return $this->python->run($arguments, $output, $input);
	}

	/**
	 * @param array<string>|string $arguments
	 * @param array<string>|null $output
	 */
	public function pip(array|string $arguments, ?array &$output = null): int {
		if (is_array($arguments)) {
			$command = trim(implode(' ', $arguments));
		} else {
			$command = trim($arguments);
		}
		if (empty($command)) {
			throw new PipCommandCannotBeExecutedException($command);
		}
		exec($this->pipCommand($command), $output, $result);
		return $result;
	}

	protected function getExecutable(): string {
		return $this->venv . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'python';
	}

	protected function pipCommand(string $command): string {
		return $this->venv . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'pip ' . $command;
	}
}
