<?php

namespace Drupal\y_lb_demo_content\Plugin\migrate\process;

use Drupal\block_content\Entity\BlockContent;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fills in link options with icon information.
 *
 * @MigrateProcessPlugin(
 *   id = "y_lb_menu_cta_block"
 * )
 */
class YLbMenuCtaBlock extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }

    // Find a Menu CTA block that was created by migration.
    $blocks = \Drupal::entityQuery('block_content')
      ->accessCheck(FALSE)
      ->condition('info', $value)
      ->execute();
    $block = BlockContent::load(reset($blocks));

    return [
      [
        'target_id' => $block->id(),
        'entity' => $block,
        'bundle' => 'menu_cta',
        ],
    ];
  }

}
