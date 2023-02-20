<?php

namespace Drupal\y_lb_demo_content\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fills in Layout Builder sections.
 *
 * @MigrateProcessPlugin(
 *   id = "lb_sections"
 * )
 */
class YLBSection extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockContentStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->blockContentStorage = $container->get('entity_type.manager')->getStorage('block_content');
    return $instance;
  }


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }

    $sections = [];
    foreach ( $value as $section ) {
      $components = [];
      $layout_id = "bootstrap_layout_builder:{$section['bs_col']}";
      $layout_settings = [
        "label" => $section['label'] ?? '',
        "container_wrapper_classes" => $section['container_wrapper_classes'] ?? '',
        "container_wrapper_attributes" => $section['container_wrapper_attributes'] ?? NULL,
        "container_wrapper" => $section['container_wrapper'] ?? [],
        "container_wrapper_bg_color_class" => $section['container_wrapper_bg_color_class'] ?? '',
        "container_wrapper_bg_media" => $section['container_wrapper_bg_media'] ?? NULL,
        "container" => $section['container'] ?? '',
        "section_classes" => $section['section_classes'] ?? '',
        "section_attributes" => $section['section_attributes'] ?? NULL,
        "regions_classes" => $section['regions_classes'] ?? [],
        "regions_attributes" => $section['regions_attributes'] ?? [],
        "breakpoints" => $section['breakpoints'] ?? [],
        "layout_regions_classes" => $section['layout_regions_classes'] ?? [],
        "context_mapping" => $section['context_mapping'] ?? [],
        "remove_gutters" => $section['remove_gutters'] ?? "0",
      ];
      if (!empty($section['components'])) {
        foreach ($section['components'] as $key => $componentConfig) {
          $blocks = $this->blockContentStorage->loadByProperties(['uuid' => $componentConfig['uuid']]);
          if (!$blocks) {
            continue;
          }
          $block = array_shift($blocks);
          $revisionId = $block->getRevisionId();
          $config = $componentConfig['config'];
          $config['block_revision_id'] = $revisionId;
          $component = new SectionComponent($componentConfig['uuid'], $componentConfig['region'], $config , $additional = []);
          $components[$componentConfig['uuid']] = $component;
        }
      }
      $section = new Section($layout_id, $layout_settings, $components, $third_party_settings = []);
      $sections[] = $section;
    }
    return $sections;
  }
}
