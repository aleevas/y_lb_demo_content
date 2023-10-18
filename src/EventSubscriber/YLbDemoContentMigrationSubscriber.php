<?php

namespace Drupal\y_lb_demo_content\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class YLbDemoContentMigrationSubscriber.
 *
 * Run functions before migrations start.
 *
 * @package Drupal\y_lb_demo_content
 */
class YLbDemoContentMigrationSubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new YLbDemoContentMigrationSubscriber instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get subscribed events.
   *
   * @inheritdoc
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::PRE_IMPORT][] = ['onMigratePreImport'];
    return $events;
  }

  /**
   * Do stuff before migration starts.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePreImport(MigrateImportEvent $event) {
    $migration_id = $event->getMigration()->getBaseId();

    if (in_array(
      $migration_id,
        [
          'y_lb_demo_menu_link_main',
          'y_lb_demo_menu_link_helpful_links'
        ]
      )
    ) {
      $map = [
        'y_lb_demo_menu_link_main' => 'main',
        'y_lb_demo_menu_link_helpful_links' => 'footer-menu-left',
      ];
      // Make cleanup the menu before import the new links.
      $this->removeMenuItems($map[$migration_id]);
    }
  }

  /**
   * Remove menu items by menu name.
   *
   * @param string $menu_name
   *
   * @return void

   */
  private function removeMenuItems(string $menu_name) {
    $menu_links = $this->entityTypeManager->getStorage('menu_link_content')
      ->loadByProperties(
        [
          'menu_name' => $menu_name,
        ]
      );

    if (is_array($menu_links)) {
      foreach ($menu_links as $menu_link) {
        $menu_link->delete();
      }
    }
  }

}
