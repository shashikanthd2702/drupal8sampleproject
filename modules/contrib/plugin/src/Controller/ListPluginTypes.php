<?php

/**
 * @file
 * Contains \Drupal\plugin\Controller\ListPluginTypes.
 */

namespace Drupal\plugin\Controller;

use Drupal\plugin\PluginType\PluginTypeInterface;

/**
 * Handles the "list plugin types" route.
 */
class ListPluginTypes extends ListBase {

  /**
   * Handles the route.
   *
   * @return mixed[]
   *   A render array.
   */
  public function execute() {
    $build = [
      '#empty' => $this->t('There are no available plugin types.'),
      '#header' => [$this->t('Type'), $this->t('Description'), $this->t('Provider'), $this->t('Operations')],
      '#type' => 'table',
    ];
    $plugin_types = $this->pluginTypeManager->getPluginTypes();
    uasort($plugin_types, function (PluginTypeInterface $plugin_type_a, PluginTypeInterface $plugin_type_b) {
      return strnatcasecmp($plugin_type_a->getLabel(), $plugin_type_b->getLabel());
    });
    foreach ($plugin_types as $plugin_type_id => $plugin_type) {
      $operations_provider = $plugin_type->getOperationsProvider();
      $operations = $operations_provider ? $operations_provider->getOperations($plugin_type_id) : [];

      $build[$plugin_type_id]['label'] = [
        '#markup' => $plugin_type->getLabel(),
      ];
      $build[$plugin_type_id]['description'] = [
        '#markup' => $plugin_type->getDescription(),
      ];
      $build[$plugin_type_id]['provider'] = [
        '#markup' => $this->getProviderLabel($plugin_type->getProvider()),
      ];
      $build[$plugin_type_id]['operations'] = [
        '#links' => $operations,
        '#type' => 'operations',
      ];
    }

    return $build;
  }

}
