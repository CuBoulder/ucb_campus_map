<?php

namespace Drupal\ucb_campus_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\ucb_campus_map\MapPathManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a controller for map redirect routes.
 */
class MapRedirectController extends ControllerBase {

  /**
   * The map path manager.
   *
   * @var \Drupal\ucb_campus_map\MapPathManager
   */
  protected $mapPathManager;

  /**
   * Constructs a MapRedirectController.
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
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ucb_campus_map.map_path')
    );
  }

  /**
   * Redirects the legacy building code to the correct map URL.
   *
   * @param string $building
   *   The building code.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  protected function mapRedirect($building) {
    $building = strtoupper($building);
    $buildings = $this->config('ucb_campus_map.configuration')->get('buildings');
    $options = [];
    if (isset($buildings[$building])) {
      $options['fragment'] = '!m/' . $buildings[$building]['marker'];
    }

    $config = $this->configFactory()->get('pantheon_domain_masking.settings');
    $enabled = \filter_var($config->get('enabled', 'no'), FILTER_VALIDATE_BOOLEAN);
    if ($enabled === TRUE) {
      $subpath = $config->get('subpath', '');
      if ($subpath === 'map') {
        $path = 'https://www.colorado.edu/map';
        $map_path = $this->mapPathManager->getPath();
        if ($map_path !== MapPathManager::DEFAULT_PATH) {
          $path .= $map_path;
        }
        $path .= '/#' . ($options['fragment'] ?? '');
        $redirect = new TrustedRedirectResponse($path);
        return $redirect;
      }
    }

    return new RedirectResponse($this->mapPathManager->getUrl($options)->toString(), 301);
  }

  /**
   * Redirects the legacy building code to the correct map URL.
   *
   * The redirect is done based on the `bldg` query parameter of the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function legacyMapRedirect(Request $request) {
    return $this->mapRedirect($request->query->get('bldg'));
  }

}
