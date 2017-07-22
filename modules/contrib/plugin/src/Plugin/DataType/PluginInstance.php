<?php

/**
 * @file
 * Contains \Drupal\plugin\Plugin\DataType\PluginInstance.
 */

namespace Drupal\plugin\Plugin\DataType;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedData;
use Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemInterface;

/**
 * Provides a plugin instance data type.
 *
 * @DataType(
 *   id = "plugin_instance",
 *   label = @Translation("Plugin instance")
 * )
 */
class PluginInstance extends TypedData {

  // @todo Stop using this once https://www.drupal.org/node/2615790 is fixed.
  use DependencySerializationTrait;

  /**
   * The plugin instance.
   *
   * @var \Drupal\Component\Plugin\PluginInspectionInterface
   */
  protected $value;

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    if (!$value instanceof PluginInspectionInterface) {
      $value = NULL;
    }
    parent::setValue($value, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function __clone() {
    if ($this->getValue()) {
      $this->setValue(clone $this->value);
    }
  }

}
