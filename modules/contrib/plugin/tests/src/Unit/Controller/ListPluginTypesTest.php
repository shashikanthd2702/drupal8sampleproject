<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\Controller\ListPluginTypesTest.
 */

namespace Drupal\Tests\plugin\Unit\Controller;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\plugin\Controller\ListPluginTypes;
use Drupal\plugin\PluginType\PluginType;
use Drupal\plugin\PluginType\PluginTypeManagerInterface;
use Drupal\Tests\plugin\TranslationMock;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\plugin\Controller\ListPluginTypes
 *
 * @group Plugin
 */
class ListPluginTypesTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\plugin\Controller\ListPluginTypes
   */
  protected $sut;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currentUser;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The plugin type manager.
   *
   * @var \Drupal\plugin\PluginType\PluginTypeManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $pluginTypeManager;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->pluginTypeManager = $this->getMock(PluginTypeManagerInterface::class);

    $this->stringTranslation = new TranslationMock();

    $this->sut = new ListPluginTypes($this->stringTranslation, $this->moduleHandler, $this->pluginTypeManager);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = [
      ['module_handler', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->moduleHandler],
      ['plugin.plugin_type_manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->pluginTypeManager],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $sut = ListPluginTypes::create($container);
    $this->assertInstanceOf(ListPluginTypes::class, $sut);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $class_resolver = $this->getMock(ClassResolverInterface::class);

    $plugin_manager = $this->getMock(PluginManagerInterface::class);

    $typed_config_manager = $this->getMock(TypedConfigManagerInterface::class);
    $typed_config_manager->expects($this->atLeastOnce())
      ->method('hasConfigSchema')
      ->willReturn(TRUE);

    $plugin_type_id_a = $this->randomMachineName();
    $plugin_type_label_a = $this->randomMachineName();
    $plugin_type_description_a = $this->randomMachineName();
    $plugin_type_definition_a = [
      'id' => $plugin_type_id_a,
      'label' => $plugin_type_label_a,
      'description' => $plugin_type_description_a,
      'provider' => $this->randomMachineName(),
    ];
    $plugin_type_a = new PluginType($plugin_type_definition_a, $this->stringTranslation, $class_resolver, $plugin_manager, $typed_config_manager);
    $plugin_type_id_b = $this->randomMachineName();
    $plugin_type_label_b = $this->randomMachineName();
    $plugin_type_description_b = '';
    $plugin_type_definition_b = [
      'id' => $plugin_type_id_b,
      'label' => $plugin_type_label_b,
      'description' => $plugin_type_description_b,
      'provider' => $this->randomMachineName(),
    ];
    $plugin_type_b = new PluginType($plugin_type_definition_b, $this->stringTranslation, $class_resolver, $plugin_manager, $typed_config_manager);

    $plugin_types = [
      $plugin_type_id_a => $plugin_type_a,
      $plugin_type_id_b => $plugin_type_b,
    ];

    $this->pluginTypeManager->expects($this->atLeastOnce())
      ->method('getPluginTypes')
      ->willReturn($plugin_types);

    $build = $this->sut->execute();

    $this->assertSame((string) $build[$plugin_type_id_a]['label']['#markup'], $plugin_type_label_a);
    $this->assertSame((string) $build[$plugin_type_id_a]['description']['#markup'], $plugin_type_description_a);
    $this->assertSame((string) $build[$plugin_type_id_b]['label']['#markup'], $plugin_type_label_b);
    $this->assertSame((string) $build[$plugin_type_id_b]['description']['#markup'], $plugin_type_description_b);
  }

}
