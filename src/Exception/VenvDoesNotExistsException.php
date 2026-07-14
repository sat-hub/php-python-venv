<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Exception;

class VenvDoesNotExistsException extends PHPPythonVenvException
{
	public function __construct(string $path) {
		parent::__construct('There is no Python venv in ' . $path . '.');
	}
}
