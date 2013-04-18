<?php
/**
 * @author Filipe Dobreira <http://github.com/filp>
 * Bootstraper for PHPUnit tests.
 */
error_reporting(E_ALL | E_STRICT);
$_ENV['gimme-test'] = true;
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Gimme\\', __DIR__);
