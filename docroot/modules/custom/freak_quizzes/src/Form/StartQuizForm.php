<?php declare(strict_types = 1);

namespace Drupal\freak_quizzes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Provides a Freak Quizzes form.
 */
final class StartQuizForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'freak_quizzes_start_quiz';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $current_user = \Drupal::currentUser();
    $user_name = $current_user->getAccountName();
    $user_email = $current_user->getEmail();

    $form['user_info'] = [
      '#markup' => $this->t('<h2>Hello @name, wellcome to Freak Quizzes.  </h2>', [
        '@name' => $user_name,
        '@email' => $user_email,
      ]),
    ];

    $uids = \Drupal::entityQuery('user')
      ->accessCheck(FALSE)
      ->condition('name', $current_user->getAccountName(), 'IN')
      ->execute();

    $users = User::loadMultiple($uids);
    $user = array_shift($users);
    $currentScore = (int)implode('', array_map(function ($score) { return $score['value']; }, $user->field_quizzes_score->getValue())) ?? 0;
    $form['user_score'] = [
      '#markup' => $this->t('<h3>Current score: @score</h3>', [
        '@score' => $currentScore,
      ]),
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Start Quiz'),
      ],
    ];

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
    $form_state->setRedirect('freak_quizzes.quiz');
  }

}
