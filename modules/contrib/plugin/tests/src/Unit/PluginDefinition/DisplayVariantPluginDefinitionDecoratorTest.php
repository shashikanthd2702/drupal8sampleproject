<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\PluginDefinition\DisplayVariantPluginDefinitionDecoratorTest.
 */

namespace Drupal\Tests\plugin\Unit\PluginDefinition;

use Drupal\plugin\PluginDefinition\DisplayVariantPluginDefinitionDecorator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin\PluginDefinition\DisplayVariantPluginDefinitionDecorator
 *
 * @group Plugin
 */
class DisplayVariantPluginDefinitionDecoratorTest extends UnitTestCase {

  /**
   * The array definition.
   *
   * @var mixed[]
   */
  protected $arrayDefinition = [];

  /**
   * The subject under test.
   *
   * @var \Drupal\plugin\PluginDefinition\DisplayVariantPluginDefinitionDecorator
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->arrayDefinition = [
      'admin_label' => $this->randomMachineName(),
    ];

    $this->sut = new DisplayVariantPluginDefinitionDecorator($this->arrayDefinition);
  }

  /**
   * @covers ::setLabel
   * @covers ::getLabel
   */
  public function testGetLabel() {
    // Test the injected value.
    $this->assertSame($this->arrayDefinition['admin_label'], $this->sut->getLabel());
    $this->assertSame($this->arrayDefinition['admin_label'], $this->sut->getArrayDefinition()['admin_label']);
    $this->assertSame($this->arrayDefinition['admin_label'], $this->sut['admin_label']);

    // Test changing the value through the setter.
    $value = $this->randomMachineName();
    $this->assertSame($this->sut, $this->sut->setLabel($value));
    $this->assertSame($value, $this->sut->getLabel());
    $this->assertSame($value, $this->sut->getArrayDefinition()['admin_label']);
    $this->assertSame($value, $this->sut['admin_label']);

    // Test changing the value through array access.
    $value = $this->randomMachineName();
    $this->sut['admin_label'] = $value;
    $this->assertSame($value, $this->sut->getLabel());
    $this->assertSame($value, $this->sut->getArrayDefinition()['admin_label']);
    $this->assertSame($value, $this->sut['admin_label']);

    // Test unsetting the value.
    unset($this->sut['admin_label']);
    $this->assertFalse(isset($this->sut['admin_label']));
    $this->assertNull($this->sut->getLabel());
    $this->assertFalse(isset($this->sut->getArrayDefinition()['admin_label']));
  }

}
