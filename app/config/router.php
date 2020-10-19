<?php


$router = $di->getRouter();


$router->removeExtraSlashes(true);

$router->add('/', [
    'controller' => 'index',
    'action' => 'index'
])->setName('index');

$router->add('register', [
   'controller' => 'users',
   'action' => 'register'
])->setName('register');

$router->add('login', [
   'controller' => 'users',
   'action' => 'login'
])->setName('login');

$router->add('user-area', [
    'controller' => 'users',
    'action' => 'userArea'
])->setName('user-area');


$router->handle($_SERVER['REQUEST_URI']);
