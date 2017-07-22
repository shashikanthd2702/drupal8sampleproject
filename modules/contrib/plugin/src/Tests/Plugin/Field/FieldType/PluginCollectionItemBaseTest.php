<?php

/**
 * @file
 * Contains \Drupal\plugin\Tests\Plugin\Field\FieldType\PluginCollectionItemBaseTest.
 */

namespace Drupal\plugin\Tests\Plugin\Field\FieldType;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin;
use Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockManager;
use Drupal\simpletest\KernelTestBase;

/**
 * Tests \Drupal\plugin\Plugin\Field\Plugin\Field\FieldType\PluginCollectionItemBase.
 *
 * @group Plugin
 */
class PluginCollectionItemBaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['plugin', 'plugin_test_helper', 'plugin_test'];

  /**
   * The field item under test.
   *
   * @var \Drupal\plugin\Plugin\Field\FieldType\PluginCollectionItemBase
   */
  protected $fieldItem;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $field_definition = BaseFieldDefinition::create('plugin:plugin_test_helper_mock');

    /** @var \Drupal\Core\Field\FieldItemListInterface $field_item_list */
    $field_item_list = \Drupal::typedDataManager()->create($field_definition);
    $field_item_list->appendItem();

    $this->fieldItem = $field_item_list->first();
  }

  /**
   * Tests the field.
   */
  protected function testField() {
    $plugin_id = 'plugin_test_helper_plugin';
    $plugin_id_configurable = 'plugin_test_helper_configurable_plugin';
    $plugin_configuration = [
      'foo' => $this->randomMachineName()
    ];

    // Test default values.
    $this->assertEqual($this->fieldItem->getContainedPluginId(), '');
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), []);
    $this->assertNull($this->fieldItem->getContainedPluginInstance());

    // Test setting values and auto-instantiation for a non-configurable plugin.
    $this->fieldItem->setContainedPluginId($plugin_id);
    $this->assertEqual($this->fieldItem->getContainedPluginId(), $plugin_id);
    $this->fieldItem->setContainedPluginConfiguration($plugin_configuration);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), []);
    $this->assertEqual($this->fieldItem->getContainedPluginInstance()->getPluginId(), $plugin_id);

    // Test setting values and auto-instantiation for a configurable plugin.
    $this->fieldItem->setContainedPluginId($plugin_id_configurable);
    $this->assertEqual($this->fieldItem->getContainedPluginId(), $plugin_id_configurable);
    $this->fieldItem->setContainedPluginConfiguration($plugin_configuration);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $plugin_configuration);
    $this->assertEqual($this->fieldItem->getContainedPluginInstance()->getPluginId(), $plugin_id_configurable);
    /** @var \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin $plugin_instance_a */
    $plugin_instance_a = $this->fieldItem->getContainedPluginInstance();
    $this->assertTrue($plugin_instance_a instanceof MockConfigurablePlugin);
    $this->assertEqual($plugin_instance_a->getConfiguration(), $plugin_configuration);
    $altered_plugin_configuration = $plugin_configuration += [
      'bar' => $this->randomMachineName(),
    ];
    $plugin_instance_a->setConfiguration($altered_plugin_configuration);
    $this->assertEqual($plugin_instance_a->getConfiguration(), $altered_plugin_configuration);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $altered_plugin_configuration);

    // Test resetting the values.
    $this->fieldItem->applyDefaultValue();
    $this->assertEqual($this->fieldItem->getContainedPluginId(), '');
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), []);
    $this->assertNull($this->fieldItem->getContainedPluginInstance());

    // Test setting values again and auto-instantiation.
    $this->fieldItem->applyDefaultValue();
    $this->fieldItem->setContainedPluginId($plugin_id_configurable);
    $this->assertEqual($this->fieldItem->getContainedPluginId(), $plugin_id_configurable);
    $this->fieldItem->setContainedPluginConfiguration($plugin_configuration);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $plugin_configuration);
    /** @var \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin $plugin_instance_b */
    $plugin_instance_b = $this->fieldItem->getContainedPluginInstance();
    $this->assertTrue($plugin_instance_b instanceof MockConfigurablePlugin);
    $this->assertEqual($plugin_instance_b->getConfiguration(), $plugin_configuration);
    // Make sure this is indeed a new instance and not the old one.
    $this->assertNotIdentical(spl_object_hash($plugin_instance_b), spl_object_hash($plugin_instance_a));
    // Make sure changing the configuration on the new instance changes the
    // configuration in the field item.
    $altered_plugin_configuration_a = $plugin_configuration + [
      'bar' => $this->randomMachineName(),
    ];
    $altered_plugin_configuration_b = $plugin_configuration + [
      'baz' => $this->randomMachineName(),
    ];
    $plugin_instance_b->setConfiguration($altered_plugin_configuration_b);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $altered_plugin_configuration_b);
    // Make sure changing the configuration on the old instance no longer has
    // any effect on the field item.
    $plugin_instance_a->setConfiguration($altered_plugin_configuration_a);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $altered_plugin_configuration_b);

    // Test feedback from the plugin back to the field item.
    $plugin_manager = new MockManager();
    /** @var \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin $plugin_instance_c */
    $plugin_configuration_c = $plugin_configuration + [
        'qux' => $this->randomMachineName(),
      ];
    $plugin_instance_c = $plugin_manager->createInstance($plugin_id_configurable, $plugin_configuration_c);
    $this->fieldItem->setContainedPluginInstance($plugin_instance_c);
    $this->assertEqual(spl_object_hash($this->fieldItem->getContainedPluginInstance()), spl_object_hash($plugin_instance_c));
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $plugin_configuration_c);
    $altered_plugin_configuration_c = $plugin_configuration_c + [
        'foobar' => $this->randomMachineName(),
      ];
    $plugin_instance_c->setConfiguration($altered_plugin_configuration_c);
    $this->assertEqual($this->fieldItem->getContainedPluginConfiguration(), $altered_plugin_configuration_c);

    // Test setting the main property.
    /** @var \Drupal\plugin_test_helper\Plugin\PluginTestHelper\MockConfigurablePlugin $plugin_instance_d */
    $plugin_instance_d = $plugin_manager->createInstance($plugin_id_configurable);
    $plugin_instance_d->setConfiguration([
      'oman' => '42',
    ]);
    $this->fieldItem->setValue($plugin_instance_d);
    $this->assertEqual(spl_object_hash($this->fieldItem->getContainedPluginInstance()), spl_object_hash($plugin_instance_d));
  }

}
