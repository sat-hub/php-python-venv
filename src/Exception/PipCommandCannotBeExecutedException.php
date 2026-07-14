<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Exception;

class PipCommandCannotBeExecutedException extends PHPPythonVenvException
{
	public function __construct(string $command) {
		$message = empty($command) ? 'empty pip command' : 'pip command "' . $command . '"';
		parent::__construct('Cannot execute ' . $message . '.');
	}
}
