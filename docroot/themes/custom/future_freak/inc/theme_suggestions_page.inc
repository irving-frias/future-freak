<?php
// phpcs:ignoreFile

/**
 * @file
 * future_freak 2022 suggestions for page.
 */

/**
 * Implements hook_theme_suggestions_page_alter().
 */
function future_freak_theme_suggestions_page_alter(array &$suggestions, array $variables) {
  future_freak_theme_suggestions_page_alter_view($suggestions, $variables);
}

/**
 * Process page suggestions for view pages.
 */
function future_freak_theme_suggestions_page_alter_view(array &$suggestions, array $variables) {
  $route = \Drupal::routeMatch();
  $route_name = $route->getRouteName();
  $route_name_parts = explode('.', $route_name);

  if (!(isset($route_name_parts[0]) && $route_name_parts[0] == 'view')) {
    return FALSE;
  }

  if (!isset($route_name_parts[2])) {
    return FALSE;
  }

  if (substr($route_name_parts[2], 0, 4) != 'page') {
    return FALSE;
  }

  $suggestions[] = 'page__view';
  $suggestions[] = 'page__view__' . $route_name_parts[1];
}
