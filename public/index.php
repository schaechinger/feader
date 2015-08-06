<?php

require_once '/var/www/boot.php';

// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

$host = $_SERVER['HTTP_HOST'];
$type = 'maintaining';
if ('test.feader.eu' === $host)
{
    $type = 'testing';
}

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', $type);

/** Zend_Application */
//require_once '../vendor/autoload.php';
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV, '/var/www/configs/application.ini'
);
$application->bootstrap()
			->run();

function ex($param)
{
    echo '<pre>';
    var_dump($param);
    echo '</pre>';
    exit;
}
