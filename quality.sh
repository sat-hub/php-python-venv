#!/bin/sh

BIN=vendor/bin

echo PHPLOC
$BIN/phploc app
echo

echo PHPUNIT
$BIN/phpunit
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
$BIN/phpcpd --min-lines 10 src test
