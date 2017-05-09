<?php

/**
 * @file
 * Copy and modification of auto generated index.php file.
 */

require_once __DIR__ . '/SwaggerServer/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Silex\Application;

$app = new Application();

$app->GET('/activities', function (Application $app, Request $request) {

  $endomondo = file_get_contents('https://www.endomondo.com/embed/user/workouts?id=1818981');
  // So far history is just a copy of the endomondo URL above with a size
  // parameter giving all activities. This is to save bandwidth and should be
  // manually updated after 30 new events or so.
  // @todo find a better solution. DB storage or such.
  $history = file_get_contents('data/history.html');
  $activities = array_merge(getActivitiesFromHTML($endomondo), getActivitiesFromHTML($history));
  applyExtras($activities);

  if (!empty($activities)) {
    return new JsonResponse(array_values($activities), 200);
  }
});

$app->GET('/projects', function (Application $app, Request $request) {
  return new JsonResponse(json_decode(file_get_contents('data/projects.json')), 200);
});


$app->run();

/**
 * Parse HTMÆ from Endomondo and local file.
 */
function getActivitiesFromHTML($data) {
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

/**
 * Enrich activity date with extras.
 */
function applyExtras(&$activities) {
  $extras = json_decode(file_get_contents('data/activity-extras.json'));
  foreach ($extras as $extra) {
    foreach ($activities as &$activity) {
      if ($activity['date'] == $extra->date && $activity['activity'] == $extra->activity && $activity['distance'] == $extra->distance && $activity['time'] == $extra->time) {
        // Identical activity found.
        $activity = array_merge($activity, (array) $extra);
        continue;
      }
    }
  }
}
