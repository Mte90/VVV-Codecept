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

if (command_exist('composer')) {
    echo "Installing WP-Browser with composer\n";
    echo exec('composer require --dev lucatume/wp-browser');
}

if (file_exists('./vendor/bin/wpcept')) {
    echo "Initialize Codeception\n";
    echo exec('./vendor/bin/wpcept bootstrap');
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
        Db:
            dsn: 'mysql:host=192.168.50.4;dbname=wordpress_unit_tests'
            user: wp
            password: wp
            dump: tests/_data/dump.sql
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

$db = str_replace('domain-replace', $path[0], file_get_contents('https://raw.githubusercontent.com/Mte90/VVV-Codecept/master/dump.sql'));
file_put_contents('tests/_data/dump.sql', $db);

echo "\nDone\n";