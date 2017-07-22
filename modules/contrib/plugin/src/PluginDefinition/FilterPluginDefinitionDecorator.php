<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginDefinition\FilterPluginDefinitionDecorator.
 */

namespace Drupal\plugin\PluginDefinition;

use Drupal\Component\Plugin\Context\ContextDefinitionInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides a filter plugin definition decorator.
 *
 * @ingroup Plugin
 */
class FilterPluginDefinitionDecorator extends ArrayPluginDefinitionDecorator {

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->arrayDefinition['title'] = $label;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return isset($this->arrayDefinition['title']) ? $this->arrayDefinition['title'] : NULL;
  }

}
