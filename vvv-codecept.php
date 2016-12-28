#!/usr/bin/php
<?php 

function command_exist($cmd) {
    $returnVal = shell_exec("which $cmd");
    return (empty($returnVal) ? false : true);
}

$vvconfig = $_SERVER['HOME'] . '/.vv-config';
if (!file_exists($vvconfig)) {
    $vvconfig = './.vv-config';
    if (!file_exists($vvconfig)) {
        echo "vv-config not found!\n";
        die();
    }
}

$vvconfig = json_decode(file_get_contents($vvconfig), true);

$path = str_replace($vvconfig['path'] . '/www/', '', $_SERVER['PWD']);
$path = explode('/', $path);

if(!isset($path[4])) {
    echo "This is not a VVV path!\n";
    die();
}

$composer = json_decode(file_get_contents("composer.json"), true);

if (!isset($composer['require-dev']['lucatume/wp-browser']) && command_exist('composer')) {
    echo "Installing WP-Browser with composer\n";
    echo exec('composer require --dev lucatume/wp-browser');
}

$composer_path = './vendor/';
if(isset($composer['config']['vendor-dir'])) {
    $composer_path = './' . $composer['config']['vendor-dir'];
}

if (file_exists($composer_path.'bin/wpcept')) {
    echo "Initialize Codeception\n";
    echo $composer_path.'bin/wpcept bootstrap';
    echo exec($composer_path.'bin/wpcept bootstrap');
}

$codeception = "actor: Tester
paths:
    tests: tests
    log: tests/_output
    data: tests/_data
    helpers: tests/_support
settings:
    bootstrap: _bootstrap.php
    colors: true
    memory_limit: 1024M
modules:
    config:
        WPBrowser:
            url: 'http://".$path[0].".dev'
            adminUsername: admin
            adminPassword: password
            adminUrl: /wp-admin
        WPDb:
            dsn: 'mysql:host=192.168.50.4;dbname=wordpress_unit_tests'
            user: wp
            password: wp
            dump: tests/_data/dump.sql
            populate: true
            cleanup: true
            url: 'http://".$path[0].".dev'
            tablePrefix: wp_
        WPLoader:
            wpRootFolder: /var/www/VVV/www/".$path[0]."/htdocs/
            dbName: wordpress_unit_tests
            dbHost: 192.168.50.4
            dbUser: wp
            dbPassword: wp
            wpDebug: true
            dbCharset: utf8
            dbCollate: ''
            tablePrefix: wp_
            domain: ".$path[0].".dev
            adminEmail: admin@wp.dev
            title: 'WP Tests'
            phpBinary: php
            language: ''
            configFile: 'wp-config-test.php'
            plugins: [\"".$path[4]."/".$path[4].".php\"]
            activatePlugins: [\"".$path[4]."/".$path[4].".php\"]
        WPWebDriver:
            url: 'http://".$path[0].".dev'
            browser: phantomjs
            port: 4444
            restart: true
            wait: 2
            adminUsername: admin
            adminPassword: password
            adminUrl: /wp-admin
";

file_put_contents('codeception.yml', $codeception);

$wpconfig = "
<?php

// ** MySQL settings ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress_unit_tests');

/** MySQL database username */
define('DB_USER', 'wp');

/** MySQL database password */
define('DB_PASSWORD', 'wp');

/** MySQL hostname */
define('DB_HOST', '192.168.50.4');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

$"."table_prefix = 'wp_';
";

file_put_contents('wp-config-test.php', $codeception);

$db = str_replace('domain-replace', $path[0], file_get_contents('https://raw.githubusercontent.com/Mte90/VVV-Codecept/master/dump.sql'));
file_put_contents('tests/_data/dump.sql', $db);

echo "\nDone\n";
