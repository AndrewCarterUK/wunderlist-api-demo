<?php

require 'vendor/autoload.php';
require_once 'keys.php';

use GuzzleHttp\Client;
use Wunderlist\WunderlistClient as Wunderlist;

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader);

$guzzle = new Client(
    [
        'base_uri' => 'https://a.wunderlist.com/api/v1/',
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Client-ID' => $clientId,
            'X-Access-Token' => $accessToken,
        ]
    ]
);

$wunderlist = new Wunderlist($guzzle);

$tasks = $wunderlist->getListTasks($listId);

echo $twig->render('list.html.twig', array('tasks' => $tasks));
