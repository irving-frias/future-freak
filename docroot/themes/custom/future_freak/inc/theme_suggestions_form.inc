<?php
// phpcs:ignoreFile

/**
 * @file
 * LACCD 2022 suggestions for page title.
 */

/**
 * Implements hook_theme_suggestions_page_title_alter().
 */
function future_freak_theme_suggestions_form_alter(array &$suggestions, array $variables) {
  $suggestions[] = 'form__' . $variables['element']['#form_id'];
}
