<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if (!defined( 'BOLT_PROJECT_ROOT_DIR')) {
    if (substr(dirname(__FILE__), -21) == '/vendor/bolt/bolt/app') { // installed bolt with composer
        define('BOLT_COMPOSER_INSTALLED', true);
        define('BOLT_PROJECT_ROOT_DIR', substr(dirname(__FILE__), 0, -21));
        define('BOLT_WEB_DIR', BOLT_PROJECT_ROOT_DIR.'/web');
        define('BOLT_CONFIG_DIR', BOLT_PROJECT_ROOT_DIR.'/config');
    } else {
        define('BOLT_COMPOSER_INSTALLED', false);
        define('BOLT_PROJECT_ROOT_DIR', dirname(dirname(__FILE__)));
        define('BOLT_WEB_DIR', BOLT_PROJECT_ROOT_DIR);

        // Set the config folder location. If we haven't set the constant in index.php, use one of the
        // default values.
        if (!defined("BOLT_CONFIG_DIR")) {
            if (file_exists(dirname(__FILE__).'/config')) {
                // Default value, /app/config/..
                define('BOLT_CONFIG_DIR', dirname(__FILE__).'/config');
            } else {
                // otherwise use /config, outside of the webroot folder.
                define('BOLT_CONFIG_DIR', dirname(dirname(dirname(__FILE__))).'/config');
            }
        }
    }
}

// First, do some low level checks, like whether autoload is present, the cache
// folder is writable, if the minimum PHP version is present, etc.
require_once dirname(__FILE__).'/classes/lib.php';
require_once dirname(__FILE__).'/classes/lowlevelchecks.php';

$checker = new LowlevelChecks();
$checker->doChecks();

// Let's get on with the rest..
require_once BOLT_PROJECT_ROOT_DIR.'/vendor/autoload.php';
require_once __DIR__.'/classes/util.php';

// Create the 'Bolt application'.
$app = new Bolt\Application();

// Finally, check if the app/database folder is writable, if it needs to be.
$checker->doDatabaseCheck($app['config']);

// Initialize the 'Bolt application': Set up all routes, providers, database, templating, etc..
$app->initialize();
