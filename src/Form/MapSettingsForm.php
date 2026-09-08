<?php

namespace Drupal\ucb_campus_map\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ucb_campus_map\MapPathManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure which path displays the campus map.
 */
class MapSettingsForm extends ConfigFormBase {

  /**
   * The router builder.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routerBuilder;

  /**
   * The map path manager.
   *
   * @var \Drupal\ucb_campus_map\MapPathManager
   */
  protected $mapPathManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->routerBuilder = $container->get('router.builder');
    $instance->mapPathManager = $container->get('ucb_campus_map.map_path');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [MapPathManager::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ucb_campus_map_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['map_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Map path'),
      '#description' => $this->t('The path of the page the campus map should override. Use %front for the homepage, or the path of an existing page such as %map. Create the page first; the map will replace it the same way it replaces the homepage.', [
        '%front' => '/',
        '%map' => '/map',
      ]),
      '#default_value' => $this->mapPathManager->getPath(),
      '#required' => TRUE,
      '#size' => 40,
      '#placeholder' => '/map',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $path = $this->mapPathManager->normalizePath($form_state->getValue('map_path'));
    $form_state->setValue('map_path', $path);

    if ($path === MapPathManager::DEFAULT_PATH) {
      return;
    }

    if (!preg_match('/^\/[a-zA-Z0-9\/_-]+$/', $path)) {
      $form_state->setErrorByName('map_path', $this->t('Enter a path starting with a slash, such as %map, or %front for the homepage. Use only letters, numbers, hyphens, and underscores.', [
        '%map' => '/map',
        '%front' => '/',
      ]));
      return;
    }

    if ($path === MapPathManager::LEGACY_REDIRECT_PATH) {
      $form_state->setErrorByName('map_path', $this->t('The path %path is reserved for legacy map redirects.', [
        '%path' => $path,
      ]));
      return;
    }

    if (preg_match('/^\/(admin|user|system)(\/|$)/', $path)) {
      $form_state->setErrorByName('map_path', $this->t('The path %path cannot be used for the campus map.', [
        '%path' => $path,
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(MapPathManager::CONFIG_NAME)
      ->set('map_path', $form_state->getValue('map_path'))
      ->save();
    $this->routerBuilder->rebuild();
    parent::submitForm($form, $form_state);
  }

}
