<?php

namespace Drupal\ucb_campus_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a controller for map redirect rotues.
 */
class MapRedirectController extends ControllerBase {

  /**
   * Redirects the legacy building code to the correct map URL.
   *
   * @param string $building
   *   The building code.
   *
   * @return \Drupal\Core\Routing\TrustedRedirectResponse
   *   A redirect response.
   */
  protected function mapRedirect($building) {
    $building = strtoupper($building);
    $buildings = $this->config('ucb_campus_map.configuration')->get('buildings');
    $options = [];
    if (isset($buildings[$building])) {
      $options['fragment'] = '!m/' . $buildings[$building]['marker'];
    }
    return $this->redirect('<front>', [], $options, 301);
  }

  /**
   * Redirects the legacy building code to the correct map URL.
   *
   * The redirect is done based on the `bldg` query parameter of the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incomming request.
   *
   * @return \Drupal\Core\Routing\TrustedRedirectResponse
   *   A redirect response.
   */
  public function legacyMapRedirect(Request $request) {
    return $this->mapRedirect($request->query->get('bldg'));
  }

}
