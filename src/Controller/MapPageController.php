<?php

namespace Drupal\ucb_campus_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ucb_campus_map\MapPathManager;

/**
 * Provides a controller for the campus map page.
 */
class MapPageController extends ControllerBase {

  /**
   * Builds the campus map page.
   *
   * Page chrome is replaced by page--campus-map.html.twig; this render array
   * exists so Drupal has a route to resolve.
   *
   * @return array
   *   A render array.
   */
  public function view() {
    return [
      '#markup' => '',
      '#cache' => [
        'tags' => ['config:' . MapPathManager::CONFIG_NAME],
      ],
    ];
  }

}
