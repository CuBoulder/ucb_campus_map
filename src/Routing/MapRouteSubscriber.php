<?php

namespace Drupal\ucb_campus_map\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\ucb_campus_map\MapPathManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Registers a route for the campus map when it is not on the homepage.
 */
class MapRouteSubscriber extends RouteSubscriberBase {

  /**
   * The map path manager.
   *
   * @var \Drupal\ucb_campus_map\MapPathManager
   */
  protected $mapPathManager;

  /**
   * Constructs a MapRouteSubscriber.
   *
   * @param \Drupal\ucb_campus_map\MapPathManager $map_path_manager
   *   The map path manager.
   */
  public function __construct(MapPathManager $map_path_manager) {
    $this->mapPathManager = $map_path_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($this->mapPathManager->isFrontPageMap()) {
      return;
    }

    $route = new Route(
      $this->mapPathManager->getPath(),
      [
        '_controller' => '\Drupal\ucb_campus_map\Controller\MapPageController::view',
        '_title' => 'Campus Map',
      ],
      [
        '_permission' => 'access content',
      ]
    );
    $collection->add(MapPathManager::ROUTE_NAME, $route);
  }

}
