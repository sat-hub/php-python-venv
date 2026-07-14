<?php
declare(strict_types = 1);
namespace SATHub\PHPPythonVenv\Exception;

class PythonInterpreterNotFoundException extends PHPPythonVenvException
{
	public function __construct() {
		parent::__construct('Python interpreter not found.');
	}
}
