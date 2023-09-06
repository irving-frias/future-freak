<?php declare(strict_types = 1);

namespace Drupal\freak_quizzes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Provides a Freak Quizzes form.
 */
final class QuizForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'freak_quizzes_quiz';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $current_user = \Drupal::currentUser();
    $uids = \Drupal::entityQuery('user')
      ->accessCheck(FALSE)
      ->condition('name', $current_user->getAccountName(), 'IN')
      ->execute();

    $users = User::loadMultiple($uids);
    $user = array_shift($users);
    $QuizzesAnswered = array_map(function ($quiz) { return $quiz['target_id']; }, $user->field_quizzes_answered->getValue()) ?? [];

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'freak_quizzes_question', 'IN')
      ->range(0, 10);

    if (!empty($QuizzesAnswered)) {
      $nids->condition('nid', $QuizzesAnswered, 'NOT IN');
    }

    $nids = $nids->execute();

    if (empty($nids)) {
      $form['questions'] = [
        '#type' => 'container',
        '#markup' => '<h2>No more quizzes available, thank you!</h2>',
      ];

      return $form;
    }

    $nodes = Node::loadMultiple($nids);

    $form['questions'] = [
      '#type' => 'container',
    ];

    foreach ($nodes as $node) {
      // Render each quiz question (you can customize this as needed).
      $form['questions']['question_' . $node->id()] = [
        '#markup' => '<h3>' . $node->getTitle() . '</h3>',
      ];

      $correctAnswer = array_map(function ($answer) {
        return $answer['value'];
      }, $node->field_freak_quizzes_true_a->getValue()) ?? [];

      $incorrectAnswers = explode(', ', $node->field_freak_quizzes_false_a->getValue()[0]['value']) ?? [];
      $answers = array_merge($correctAnswer, $incorrectAnswers);

      // Use a unique name for each set of radio buttons based on the question's ID.
      $radio_name = 'question_' . $node->id() . '_answers';

      $form['questions']['question_' . $node->id()]['answers'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['answers'],
        ],
      ];

      $form['questions']['question_' . $node->id()]['answers']['correct_' . $node->id()] = [
        '#type' => 'radios',
        '#options' => $answers,
        '#required' => FALSE,
        '#name' => $radio_name, // Unique name for this set of radio buttons.
      ];
    }


    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
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
    $pointsPerAnswer = 5;
    $pointsTotal = 0;
    $submitted_values = $form_state->getValues();
    $current_user = \Drupal::currentUser();
    $uids = \Drupal::entityQuery('user')
      ->accessCheck(FALSE)
      ->condition('name', $current_user->getAccountName(), 'IN')
      ->execute();

    $users = User::loadMultiple($uids);
    $user = array_shift($users);
    $QuizzesAnswered = array_map(function ($quiz) { return $quiz['target_id']; }, $user->field_quizzes_answered->getValue()) ?? [];
    $QuizzesAnsweredCorrectly = [];
    $countAnswerCorrects = 0;

    $nids = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', 'freak_quizzes_question', 'IN')
      ->range(0, 10);

    if (!empty($QuizzesAnswered)) {
      $nids->condition('nid', $QuizzesAnswered, 'NOT IN');
    }

    $nids = $nids->execute();

    $nodes = Node::loadMultiple($nids);

    foreach ($submitted_values as $element_name => $selected_value) {
      // Check if the element is a radio button (starts with 'correct_').
      if (strpos($element_name, 'correct_') === 0) {
        // Extract the question ID from the element name.
        $question_id = str_replace('correct_', '', $element_name);

        // Use the question ID to retrieve the label associated with the selected value.
        $label = $form['questions']['question_' . $question_id]['answers']['correct_' . $question_id]['#options'][$selected_value] ?? '';
        $correctAnswer = array_map(function ($answer) {
          return $answer['value'];
        }, $nodes[$question_id]->field_freak_quizzes_true_a->getValue());
        $correctAnswer = implode('', $correctAnswer);

        if ($correctAnswer == $label) {
          $pointsTotal += $pointsPerAnswer;
          $countAnswerCorrects++;
        }

        $QuizzesAnsweredCorrectly[] = $nodes[$question_id]->id();
      }
    }


    $existing_answers = array_map(function ($score) { return $score['target_id']; }, $user->field_quizzes_answered->getValue()) ?? [];
    $QuizzesAnsweredCorrectly = array_merge($existing_answers, $QuizzesAnsweredCorrectly);
    $user->field_quizzes_answered->setValue($QuizzesAnsweredCorrectly);
    $currentScore = $user->field_quizzes_score->getValue() ?? 0;
    $currentScore = (int)implode('', array_map(function ($score) { return $score['value']; }, $user->field_quizzes_score->getValue())) ?? 0;
    $currentScore += $pointsTotal;
    $user->field_quizzes_score->setValue($currentScore);
    $user->save();

    $this->messenger()->addStatus($this->t('You answered @count questions correctly', ['@count' => $countAnswerCorrects]));
    $this->messenger()->addStatus($this->t('You won @points points', ['@points' => $pointsTotal]));
    $form_state->setRedirect('freak_quizzes.start_quiz');
  }

}
