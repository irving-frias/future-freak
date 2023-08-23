<?php declare(strict_types = 1);

namespace Drupal\rick_and_morty\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Rick and Morty settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'rick_and_morty_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['rick_and_morty.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    // API URL -> https://rickandmortyapi.com/api
    $form['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API URL'),
      '#default_value' => $this->config('rick_and_morty.settings')->get('api_url'),
    ];

    $form['api_url_characters_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Characters Endpoint'),
      '#default_value' => $this->config('rick_and_morty.settings')->get('api_url_characters_endpoint'),
    ];

    $form['api_url_locations_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Locations Endpoint'),
      '#default_value' => $this->config('rick_and_morty.settings')->get('api_url_locations_endpoint'),
    ];

    $form['api_url_episodes_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Episodes Endpoint'),
      '#default_value' => $this->config('rick_and_morty.settings')->get('api_url_episodes_endpoint'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if ($form_state->getValue('example') === 'wrong') {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('The value is not correct.'),
    //     );
    //   }
    // @endcode
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('rick_and_morty.settings')
      ->set('api_url', $form_state->getValue('api_url'))
      ->save();

    $this->config('rick_and_morty.settings')
    ->set('api_url_characters_endpoint', $form_state->getValue('api_url_characters_endpoint'))
    ->save();

    $this->config('rick_and_morty.settings')
      ->set('api_url_locations_endpoint', $form_state->getValue('api_url_locations_endpoint'))
      ->save();

    $this->config('rick_and_morty.settings')
    ->set('api_url_episodes_endpoint', $form_state->getValue('api_url_episodes_endpoint'))
    ->save();
    parent::submitForm($form, $form_state);
  }

}
