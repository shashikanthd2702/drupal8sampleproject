<?php

/**
 * @file
 * Contains \Drupal\plugin\Controller\ListPlugins.
 */

namespace Drupal\plugin\Controller;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\plugin\PluginDefinition\PluginDescriptionDefinitionInterface;
use Drupal\plugin\PluginDefinition\PluginLabelDefinitionInterface;
use Drupal\plugin\PluginDefinition\PluginOperationsProviderDefinitionInterface;
use Drupal\plugin\PluginDiscovery\TypedDefinitionEnsuringPluginDiscoveryDecorator;
use Drupal\plugin\PluginType\PluginTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles the "list plugin" route.
 */
class ListPlugins extends ListBase {

  /**
   * The class resolver.
   *
   * @var \Drupal\Core\DependencyInjection\ClassResolverInterface
   */
  protected $classResolver;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\plugin\PluginType\PluginTypeManagerInterface $plugin_type_manager
   *   The plugin type manager.
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $class_resolver
   *   The class resolver.
   */
  public function __construct(TranslationInterface $string_translation, ModuleHandlerInterface $module_handler, PluginTypeManagerInterface $plugin_type_manager, ClassResolverInterface $class_resolver) {
    parent::__construct($string_translation, $module_handler, $plugin_type_manager);
    $this->classResolver = $class_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('string_translation'), $container->get('module_handler'), $container->get('plugin.plugin_type_manager'), $container->get('class_resolver'));
  }

  /**
   * Returns the route's title.
   *
   * @param string $plugin_type_id
   *   The plugin type ID.
   *
   * @return string
   */
  public function title($plugin_type_id) {
    return $this->t('%label plugins', [
      '%label' => $this->pluginTypeManager->getPluginType($plugin_type_id)->getLabel(),
    ]);
  }

  /**
   * Handles the route.
   *
   * @param string $plugin_type_id
   *   The plugin type ID.
   *
   * @return mixed[]|\Symfony\Component\HttpFoundation\Response
   *   A render array or a Symfony response.
   */
  public function execute($plugin_type_id) {
    if (!$this->pluginTypeManager->hasPluginType($plugin_type_id)) {
      throw new NotFoundHttpException();
    }
    $plugin_type = $this->pluginTypeManager->getPluginType($plugin_type_id);

    $build = [
      '#empty' => $this->t('There are no available plugins.'),
      '#header' => [$this->t('Plugin'), $this->t('ID'), $this->t('Description'), $this->t('Provider'), $this->t('Operations')],
      '#type' => 'table',
    ];
    $plugin_discovery = new TypedDefinitionEnsuringPluginDiscoveryDecorator($plugin_type);
    /** @var \Drupal\plugin\PluginDefinition\PluginDefinitionInterface[] $plugin_definitions */
    $plugin_definitions = $plugin_discovery->getDefinitions();
    ksort($plugin_definitions);
    foreach ($plugin_definitions as $plugin_definition) {
      $operations = [];
      if ($plugin_definition instanceof PluginOperationsProviderDefinitionInterface) {
        $operations_provider_class = $plugin_definition->getOperationsProviderClass();
        if ($operations_provider_class) {
          /** @var \Drupal\plugin\PluginOperationsProviderInterface $operations_provider */
          $operations_provider = $this->classResolver->getInstanceFromDefinition($operations_provider_class);
          $operations = $operations_provider->getOperations($plugin_definition->getId());
        }
      }
      $build[$plugin_definition->getId()] = [
        'label' => [
          '#markup' => $plugin_definition instanceof PluginLabelDefinitionInterface ? (string) $plugin_definition->getLabel() : NULL,
        ],
        'id' => [
          '#markup' => $plugin_definition->getId(),
          '#prefix' => '<code>',
          '#suffix' => '</code>',
        ],
        'description' => [
          '#markup' => $plugin_definition instanceof PluginDescriptionDefinitionInterface ? (string) $plugin_definition->getDescription() : NULL,
        ],
        'provider' => [
          '#markup' => $this->getProviderLabel($plugin_definition->getProvider()),
        ],
        'operations' => [
          '#links' => $operations,
          '#type' => 'operations',
        ],
      ];
    }

    return $build;
  }

}
