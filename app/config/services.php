<?php
declare(strict_types=1);

use Phalcon\Escaper;
use Phalcon\Events\Manager;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Url as UrlResolver;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});


/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'path' => $config->application->cacheDir,
                'separator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    $escaper = new Escaper();
    $flash = new Flash($escaper);
    $flash->setImplicitFlush(false);
    $flash->setCssClasses([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);

    return $flash;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionManager();
    $files = new SessionAdapter([
        'savePath' => sys_get_temp_dir(),
    ]);
    $session->setAdapter($files);
    $session->start();

    return $session;
});

$di->setShared('acl', function () {
    $acl = new Memory();

    $acl->addRole(new Role('admin'));
    $acl->addRole(new Role('user'));
    $acl->addRole(new Role('guest'));

    $acl->addComponent('users', [
        'userArea',
        'register',
        'login',
    ]);

    $acl->addComponent('index', [
        'index'
    ]);

    $acl->allow('admin', 'users', 'userArea');
    $acl->allow('admin', 'index', 'index');
    $acl->allow('user', 'index', 'index');
    $acl->allow('user', 'users', 'userArea');
    $acl->allow('guest', 'users', ['register', 'login']);
    $acl->allow('guest', 'index', 'index');
    $acl->allow('guest', 'index', 'index');

    return $acl;
});

$di->setShared('dispatcher', function () use ($di) {
    $eventsManager = new Manager();
    $eventsManager->attach('dispatch:beforeDispatch', function () use ($di) {

        // Auto Login with Authentication Key
        $users_controller = new UsersController();
        $users_controller->initialize();
        $users_controller->authenticateKeyChecker($_COOKIE['authentication_key']);

        if (!$di->getShared('session')->get('user_logged_in')) {
            $di->getShared('session')->set('user_logged_in', ['user_type' => 'guest']);
        }

        if (!$di->getShared('acl')->isAllowed($di->getShared('session')->get('user_logged_in')['user_type'], $di->getShared('router')->getControllerName(), $di->getShared('router')->getActionName())) {
            $di->getShared('view')->pick('errors/403');
            return;
        }
    });

    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});