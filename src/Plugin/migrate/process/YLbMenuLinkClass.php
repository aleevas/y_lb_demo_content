<?php

namespace Drupal\y_lb_demo_content\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fills in link options with icon information.
 *
 * @MigrateProcessPlugin(
 *   id = "y_lb_menu_link_class"
 * )
 */
class YLbMenuLinkClass extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }
    $options = [
      'attributes' => [
        'class' => $value,
      ],
    ];
    return $options;
  }

}
