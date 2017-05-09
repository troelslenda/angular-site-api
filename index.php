<?php

/**
 * @file
 * Copy and modification of auto generated index.php file.
 */

require_once __DIR__ . '/SwaggerServer/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Silex\Application;

$app = new Application();



$app->GET('/api2/activities', function (Application $app, Request $request) {

  $endomondo = file_get_contents('https://www.endomondo.com/embed/user/workouts?id=1818981');
  // So far history is just a copy of the endomondo URL above with a size
  // parameter giving all activities. This is to save bandwidth and should be
  // manually updated after 30 new events or so.
  // @todo find a better solution. DB storage or such.
  $history = file_get_contents('history.html');
  $activities = array_merge(getRows($endomondo), getRows($history));

  if (!empty($activities)) {
    return new JsonResponse(array_values($activities), 200);
  }

});


$app->GET('/api2/projects', function (Application $app, Request $request) {


  return new Response('How about implementing projectsGet as a GET method ?');
});


$app->run();

function getRows($data) {
  $dom = new DOMDocument();
  $dom->loadHTML($data);
  $data_rows = array();
  $rows = $dom->getElementsByTagName('tr');
  foreach ($rows as $row) {
    $cells = $row->getElementsByTagName('td');
    $data_cells = array();
    if (isset($cells->item(0)->nodeValue)) {
      $data_cells['date'] = $cells->item(0)->nodeValue;
      $data_cells['activity'] = $cells->item(1)->nodeValue;
      $data_cells['distance'] = $cells->item(2)->nodeValue;
      $data_cells['time'] = $cells->item(3)->nodeValue;
      // Make sure that there are no duplicate activities.
      $data_rows[implode('-', array_values($data_cells))] = $data_cells;
    }
  }
  return $data_rows;
}
