<?php


ini_set('display_errors','On');

require_once __DIR__.'/../vendor/autoload.php';

use
    AuthProvider,
    Symfony\Component\Yaml\Yaml,
    Silex\Provider\SecurityServiceProvider,
    Silex\Provider\SessionServiceProvider,
    Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder
;

/** Приложение @var Silex */
$app = new Silex\Application(); 

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../config/parameters.yml"));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new PlaintextPasswordEncoder();
});

$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'secured' => array(
        'pattern' => '^.*$',
        'http' => true,
        'stateless' => false,
        'users' => array(
            $app['parameters']['user'] => array('ROLE_ADMIN', $app['parameters']['password']),
        ),
    ),
);
