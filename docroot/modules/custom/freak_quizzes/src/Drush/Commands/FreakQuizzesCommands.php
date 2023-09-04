<?php

namespace Drupal\freak_quizzes\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 */
final class FreakQuizzesCommands extends DrushCommands {

  /**
   * Constructs a FreakQuizzesCommands object.
   */
  public function __construct(
    private readonly Token $token,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('token'),
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * Command description here.
   */
  #[CLI\Command(name: 'freak_quizzes:import-quizzes', aliases: ['fq:import-quizzes'])]
  #[CLI\Usage(name: 'freak_quizzes:import-quizzes', description: 'Import quizzes from api rest.')]
  public function importQuizzes() {
    freak_quizzes_fetch_questions();
    $this->logger()->success(dt('Achievement unlocked.'));
  }

}
