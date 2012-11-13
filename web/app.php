<?php

// Подключаем инициализацию silex
require_once __DIR__.'/../src/init.php';
require_once __DIR__.'/../src/server/upload.class.php';

use
	Symfony\Component\HttpFoundation\Response
;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array());
})->bind('home');

$app->match('/server', function () use ($app) {

    $response = new Response();

	$upload_handler = new UploadHandler($response, array(
		'upload_dir' => $app['parameters']['folder'].'files/',
		'image_versions' => array(
			'thumbnail' => array(
				'upload_dir' => $app['parameters']['folder'].'thumbs/',
			)
		)
	));

    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
    $response->headers->set('Content-Disposition', 'inline; filename="files.json"');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');

    switch ($app['request']->getMethod()) {
		case 'GET':
		    $upload_handler->get();
		    break;
		case 'POST':
	        $upload_handler->post();
		    break;
		case 'DELETE':
		    $upload_handler->delete();
		    break;
    }

    return $response;

	// switch ($_SERVER['REQUEST_METHOD']) {
	//     case 'OPTIONS':
	//         break;
	//     case 'HEAD':
	//     case 'GET':
	//         $upload_handler->get();
	//         break;
	//     case 'POST':
	//         if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
	//             $upload_handler->delete();
	//         } else {
	//             $upload_handler->post();
	//         }
	//         break;
	//     case 'DELETE':
	//         $upload_handler->delete();
	//         break;
	//     default:
	//         header('HTTP/1.1 405 Method Not Allowed');
	// }

})->method('GET|POST|DELETE');

$app->run();