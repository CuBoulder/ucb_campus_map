<?php

namespace Drupal\ucb_campus_map;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Url;
use Drupal\path_alias\AliasManagerInterface;

/**
 * Resolves the configured campus map path.
 */
class MapPathManager {

  /**
   * The configuration object name.
   */
  const CONFIG_NAME = 'ucb_campus_map.configuration';

  /**
   * The default map path (homepage).
   */
  const DEFAULT_PATH = '/';

  /**
   * The reserved path used for legacy building-code redirects.
   */
  const LEGACY_REDIRECT_PATH = '/map.html';

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * The current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Constructs a MapPathManager.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path matcher.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path stack.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    PathMatcherInterface $path_matcher,
    CurrentPathStack $current_path,
    AliasManagerInterface $alias_manager,
  ) {
    $this->configFactory = $config_factory;
    $this->pathMatcher = $path_matcher;
    $this->currentPath = $current_path;
    $this->aliasManager = $alias_manager;
  }

  /**
   * Returns the normalized map path.
   *
   * @return string
   *   An internal path such as "/" or "/map".
   */
  public function getPath() {
    $path = $this->configFactory->get(self::CONFIG_NAME)->get('map_path');
    return $this->normalizePath(is_string($path) && $path !== '' ? $path : self::DEFAULT_PATH);
  }

  /**
   * Whether the map is configured to take over the site homepage.
   *
   * @return bool
   *   TRUE if the map path is the homepage.
   */
  public function isFrontPageMap() {
    return $this->getPath() === self::DEFAULT_PATH;
  }

  /**
   * Whether the current request is the campus map page.
   *
   * @return bool
   *   TRUE if the current page should display the map template.
   */
  public function isMapPage() {
    if ($this->isFrontPageMap()) {
      return $this->pathMatcher->isFrontPage();
    }

    $map_path = $this->getPath();
    foreach ($this->getCurrentPaths() as $path) {
      if ($this->normalizePath($path) === $map_path) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns a URL object for the campus map page.
   *
   * @param array $options
   *   Options to pass to the URL generator, such as a fragment.
   *
   * @return \Drupal\Core\Url
   *   The map URL.
   */
  public function getUrl(array $options = []) {
    if ($this->isFrontPageMap()) {
      return Url::fromRoute('<front>', [], $options);
    }
    return Url::fromUserInput($this->getPath(), $options);
  }

  /**
   * Normalizes a user-supplied path.
   *
   * @param string $path
   *   The path to normalize.
   *
   * @return string
   *   A path starting with a slash and without a trailing slash, except "/".
   */
  public function normalizePath($path) {
    $path = trim($path);
    if ($path === '' || $path === '<front>') {
      return self::DEFAULT_PATH;
    }
    $path = '/' . ltrim($path, '/');
    if ($path !== self::DEFAULT_PATH) {
      $path = rtrim($path, '/');
    }
    return $path;
  }

  /**
   * Returns the current system path and alias for comparison.
   *
   * @return string[]
   *   The current internal path and its alias, if any.
   */
  protected function getCurrentPaths() {
    $system_path = $this->currentPath->getPath();
    return array_unique([
      $system_path,
      $this->aliasManager->getAliasByPath($system_path),
    ]);
  }

}
