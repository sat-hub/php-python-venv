#!/bin/sh

BIN=vendor/bin

echo PHPLOC
$BIN/phploc src
echo

echo PHPUNIT
XDEBUG_MODE=coverage $BIN/phpunit --coverage-text
echo

echo PHPCS
$BIN/phpcs src test
echo $?
echo

echo PHPMD
$BIN/phpmd src,test text ruleset.xml
echo $?
echo

echo PHPSTAN
$BIN/phpstan --level=max analyse src test
echo

echo PHPCPD
$BIN/phpcpd --min-lines 3 src test
