<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginDefinition\DisplayVariantPluginDefinitionDecorator.
 */

namespace Drupal\plugin\PluginDefinition;

use Drupal\Component\Plugin\Context\ContextDefinitionInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides a display variant plugin definition decorator.
 *
 * @ingroup Plugin
 */
class DisplayVariantPluginDefinitionDecorator extends ArrayPluginDefinitionDecorator {

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->arrayDefinition['admin_label'] = $label;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return isset($this->arrayDefinition['admin_label']) ? $this->arrayDefinition['admin_label'] : NULL;
  }

}
