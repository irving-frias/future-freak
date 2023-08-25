<?php declare(strict_types = 1);

namespace Drupal\rick_and_morty\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Rick and Morty form.
 */
final class ImportLocationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'rick_and_morty_import_location';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['import_location'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import Locations'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = \Drupal::config('rick_and_morty.settings');
    $nids = \Drupal::entityQuery('node')->execute();
    // $operations = [
    //     ['delete_nodes_example', [$nids]],
    // ];
    // $batch = [
    //     'title' => $this->t('Deleting All Nodes ...'),
    //     'operations' => $operations,
    //     'finished' => 'delete_nodes_finished',
    // ];
    // batch_set($batch);
  }

}
