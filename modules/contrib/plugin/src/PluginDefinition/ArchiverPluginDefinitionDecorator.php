<?php

/**
 * @file
 * Contains \Drupal\plugin\PluginDefinition\ArchiverPluginDefinitionDecorator.
 */

namespace Drupal\plugin\PluginDefinition;

use Drupal\Component\Plugin\Context\ContextDefinitionInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Provides an archiver plugin definition decorator.
 *
 * @ingroup Plugin
 */
class ArchiverPluginDefinitionDecorator extends ArrayPluginDefinitionDecorator {

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
