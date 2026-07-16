<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv;

use SATHub\PHPPythonVenv\Exception\PythonCommandCannotBeExecutedException;
use SATHub\PHPPythonVenv\Exception\PythonInterpreterNotFoundException;

class Python
{
	protected const PREFERRED_PYTHON_EXECUTABLE = 'python';

	protected const ALTERNATIVE_PYTHON_EXECUTABLE = [
		'/usr/local/bin/python',
		'/usr/bin/python',
		'/bin/python'
	];

	protected static string $customPythonExecutable = '';

	protected string $python = '';

	protected readonly string $version;

	public static function setCustomExecutable(string $path): void {
		if (self::tryExecutable($path)) {
			self::$customPythonExecutable = trim($path);
		} else {
			throw new PythonInterpreterNotFoundException();
		}
	}

	public function __construct() {
		if (self::$customPythonExecutable) {
			$this->python  = self::$customPythonExecutable;
			$this->version = self::tryExecutable(self::$customPythonExecutable);
		} else {
			$version = self::tryExecutable(self::PREFERRED_PYTHON_EXECUTABLE);
			if ($version) {
				$this->python  = self::PREFERRED_PYTHON_EXECUTABLE;
				$this->version = $version;
			} else {
				foreach (self::ALTERNATIVE_PYTHON_EXECUTABLE as $executable) {
					$version = self::tryExecutable($executable);
					if ($version) {
						$this->python  = $executable;
						$this->version = $version;
						break;
					}
				}
			}
		}
		if (empty($this->python)) {
			throw new PythonInterpreterNotFoundException();
		}
	}

	public function getVersion(): string {
		return $this->version;
	}

	/**
	 * @param array<string>|string $arguments
	 * @param array<string>|null $output
	 */
	public function run(array|string $arguments, ?array &$output = null, ?string $input = null): int {
		if (is_array($arguments)) {
			$command = trim(implode(' ', $arguments));
		} else {
			$command = trim($arguments);
		}
		if (empty($command)) {
			throw new PythonCommandCannotBeExecutedException($command);
		}
		if ($input) {
			$stdin    = StdinFile::withContent($input);
			$command .= ' < ' . $stdin;
		}
		exec($this->python . ' ' . $command, $output, $result);
		return $result;
	}

	protected static function tryExecutable(string $executable): string {
		$executable = trim($executable);
		if (!empty($executable)) {
			exec($executable . ' -V 2>&1', $output, $result);
			if ($result === 0 && isset($output[0]) && str_starts_with($output[0], 'Python ')) {
				return trim(substr($output[0], 7));
			}
		}
		return '';
	}
}
