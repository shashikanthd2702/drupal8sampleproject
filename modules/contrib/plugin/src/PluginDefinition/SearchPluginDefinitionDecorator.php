<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginDefinition\SearchPluginDefinitionDecorator.
 */

namespace Drupal\plugin\PluginDefinition;

use Drupal\Component\Plugin\Context\ContextDefinitionInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides a search plugin definition decorator.
 *
 * @ingroup Plugin
 */
class SearchPluginDefinitionDecorator extends ArrayPluginDefinitionDecorator {

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
