<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

$app = new Silex\Application();


$app->GET('/api2/activities', function(Application $app, Request $request) {
            
            
            return new Response('How about implementing activitiesGet as a GET method ?');
            });


$app->GET('/api2/projects', function(Application $app, Request $request) {
            
            
            return new Response('How about implementing projectsGet as a GET method ?');
            });


$app->run();
