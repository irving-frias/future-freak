<?php

namespace Drupal\rick_and_morty\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Site\Settings;
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
final class RickAndMortyCommands extends DrushCommands {
    /**
    * The entity type manager service.
    *
    * @var \Drupal\Core\Entity\EntityTypeManagerInterface
    */
    protected $entityTypeManager;

    /**
    * The configuration factory.
    *
    * @var \Drupal\Core\Config\ConfigFactoryInterface
    */
    protected $configFactory;

  /**
   * Constructs a RickAndMortyCommands object.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct();
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * Imports all characters from rick and morty api.
   */
  #[CLI\Command(name: 'rick_and_morty:import-characters', aliases: ['rnmic'])]
  #[CLI\Usage(name: 'rick_and_morty:import-characters rnmic', description: 'Usage description')]
  public function importCharacters() {
    $config = $this->getConfigFormSettings();
    $endpoint = $config['api_url'] . $config['api_url_characters_endpoint'];
    $total_pages = (int)$config['api_url_characters_total_pages'];

    foreach ($this->fetchDataGenerator($endpoint, $total_pages) as $item) {
        foreach ($this->fetchSingleDataGenerator($item) as $item) {
            echo $item . PHP_EOL;
        }
    }

    $this->logger()->success(dt('All characters imported sucessfully.'));
  }

  /**
   * Imports all locations from rick and morty api.
   */
  #[CLI\Command(name: 'rick_and_morty:import-locations', aliases: ['rnmil'])]
  #[CLI\Usage(name: 'rick_and_morty:import-locations rnmil', description: 'Usage description')]
  public function importLocations() {
    $config = $this->getConfigFormSettings();
    $endpoint = $config['api_url'] . $config['api_url_locations_endpoint'];
    $total_pages = (int)$config['api_url_locations_total_pages'];

    foreach ($this->fetchDataGenerator($endpoint, $total_pages) as $item) {
        foreach ($this->fetchSingleDataGenerator($item) as $item) {
            echo $item . PHP_EOL;
        }
    }

    $this->logger()->success(dt('All locations imported sucessfully.'));
  }

  /**
   * Imports all episodes from rick and morty api.
   */
  #[CLI\Command(name: 'rick_and_morty:import-episodes', aliases: ['rnmie'])]
  #[CLI\Usage(name: 'rick_and_morty:import-episodes rnmie', description: 'Usage description')]
  public function importEpisodes() {
    $config = $this->getConfigFormSettings();
    $endpoint = $config['api_url'] . $config['api_url_episodes_endpoint'];
    $total_pages = (int)$config['api_url_episodes_total_pages'];

    foreach ($this->fetchDataGenerator($endpoint, $total_pages) as $item) {
        foreach ($this->fetchSingleDataGenerator($item) as $item) {
            echo $item . PHP_EOL;
        }
    }

    $this->logger()->success(dt('All episodes imported sucessfully.'));
  }

  private function getConfigFormSettings() {
    $config = $this->configFactory->getEditable('rick_and_morty.settings');
    $settings = $config->get();

    return $settings;
  }

  private function fetchDataGenerator($endpoint, $total_pages) {
      // Simulate fetching data from a source.
      $endpoint = $endpoint . '?page=';

      $data = array_map(function ($page) use ($endpoint) {
          return $endpoint . $page;
      }, range(1, $total_pages));

      foreach ($data as $item) {
          yield $this->fetchData($item); // Yield each item one at a time.
      }
  }

  private function fetchData(string $endpoint) {
    $response = \Drupal::httpClient()->get($endpoint);
    if ($response->getStatusCode() != 200) {
        return [];
    }

    $body = $response->getBody();
    $jsonString = $body->getContents(); // Convert the stream to a string.
    $data = json_decode($jsonString, TRUE);

    return $data['results'];
  }

  private function fetchSingleDataGenerator($data) {
    foreach ($data as $item) {
        yield $this->fetchSingleData($item); // Yield each item one at a time.
    }
  }

  private function fetchSingleData($data) {
    echo $data['name'];
  }
}