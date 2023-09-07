<?php
// phpcs:ignoreFile

/**
 * @file
 * LACCD 2022 suggestions for page title.
 */

/**
 * Implements hook_theme_suggestions_page_title_alter().
 */
function future_freak_theme_suggestions_page_title_alter(array &$suggestions, array $variables) {
  future_freak_theme_suggestions_page_title_alter_node_bundle($suggestions, $variables);
}

/**
 * Process page suggestions for page title by node bundle.
 */
function future_freak_theme_suggestions_page_title_alter_node_bundle(array &$suggestions, array $variables) {
  $route = \Drupal::routeMatch();
  $node = $route->getParameter('node');
  if (!$node) {
    return;
  }

  $bundle = $node->bundle();

  $suggestions[] = 'page_title__' . $bundle;
}
