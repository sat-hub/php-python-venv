# PHP Python venv

Manage a Python venv in PHP.

1. $venv = Venv::init(<path>)
2. $venv->pip('install <package name>')
3. $venv->python('my-python-script.py', $output)
4. echo implode('\n', $output)
