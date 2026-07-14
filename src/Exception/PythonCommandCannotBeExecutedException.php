<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Exception;

class PythonCommandCannotBeExecutedException extends PHPPythonVenvException
{
	public function __construct(string $command) {
		$message = empty($command) ? 'empty Python command' : 'Python command "' . $command . '"';
		parent::__construct('Cannot execute ' . $message . '.');
	}
}
