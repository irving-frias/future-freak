<?php declare(strict_types = 1);

namespace Drupal\rick_and_morty\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Rick and Morty routes.
 */
final class RickAndMortyController extends ControllerBase {
    /**
    * The entity type manager service.
    *
    * @var \Drupal\Core\Entity\EntityTypeManagerInterface
    */
    protected $entityTypeManager;

    /**
    * The HTTP client service.
    *
    * @var \Drupal\Core\Http\ClientFactory
    */
    protected $httpClient;

  /**
   * The controller constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ClientFactory $httpClient
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('http_client'),
    );
  }

    /**
    * Method to import characters from API.
    */
    public function importCharacters($endpoint) {
        $data = $this->retrieveData($endpoint);
        $i = 0;
    }

    private function retrieveData($endpoint) {
        $response = $this->httpClient->get($endpoint);
        $data = $response->getBody()->getContents();
        return $data;
    }
}
