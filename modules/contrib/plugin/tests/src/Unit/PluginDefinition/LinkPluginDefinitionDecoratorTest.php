<?php

/**
 * @file
 * Contains \Drupal\Tests\plugin\Unit\PluginDefinition\LinkPluginDefinitionDecoratorTest.
 */

namespace Drupal\Tests\plugin\Unit\PluginDefinition;

use Drupal\plugin\PluginDefinition\LinkPluginDefinitionDecorator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\plugin\PluginDefinition\LinkPluginDefinitionDecorator
 *
 * @group Plugin
 */
class LinkPluginDefinitionDecoratorTest extends UnitTestCase {

  /**
   * The array definition.
   *
   * @var mixed[]
   */
  protected $arrayDefinition = [];

  /**
   * The subject under test.
   *
   * @var \Drupal\plugin\PluginDefinition\LinkPluginDefinitionDecorator
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->arrayDefinition = [
      'title' => $this->randomMachineName(),
    ];

    $this->sut = new LinkPluginDefinitionDecorator($this->arrayDefinition);
  }

  /**
   * @covers ::setLabel
   * @covers ::getLabel
   */
  public function testGetLabel() {
    // Test the injected value.
    $this->assertSame($this->arrayDefinition['title'], $this->sut->getLabel());
    $this->assertSame($this->arrayDefinition['title'], $this->sut->getArrayDefinition()['title']);
    $this->assertSame($this->arrayDefinition['title'], $this->sut['title']);

    // Test changing the value through the setter.
    $value = $this->randomMachineName();
    $this->assertSame($this->sut, $this->sut->setLabel($value));
    $this->assertSame($value, $this->sut->getLabel());
    $this->assertSame($value, $this->sut->getArrayDefinition()['title']);
    $this->assertSame($value, $this->sut['title']);

    // Test changing the value through array access.
    $value = $this->randomMachineName();
    $this->sut['title'] = $value;
    $this->assertSame($value, $this->sut->getLabel());
    $this->assertSame($value, $this->sut->getArrayDefinition()['title']);
    $this->assertSame($value, $this->sut['title']);

    // Test unsetting the value.
    unset($this->sut['title']);
    $this->assertFalse(isset($this->sut['title']));
    $this->assertNull($this->sut->getLabel());
    $this->assertFalse(isset($this->sut->getArrayDefinition()['title']));
  }

}
