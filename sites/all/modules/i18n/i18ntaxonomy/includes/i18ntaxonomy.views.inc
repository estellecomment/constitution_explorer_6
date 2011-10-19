<?php
// $Id$

/**
 * @file
 *
 * Provide views data and handlers for i18ntaxonomy.module
 */

/**
 * Implementation of hook_views_data_alter().
 *
 * Add translation information to the term_data table.
 */
function i18ntaxonomy_views_data_alter(&$data) {
  // Language field
  $data['term_data']['language'] = array(
    'group' => t('Term translation'),
    'title' => t('Language'),
    'help' => t('The language the term is in.'),
    'field' => array(
      'handler' => 'i18ntaxonomy_handler_field_term_language',
      'click sortable' => TRUE,
    ),
    'filter' => array(
      'handler' => 'i18ntaxonomy_handler_filter_term_language',
    ),
    'argument' => array(
      'handler' => 'i18ntaxonomy_handler_argument_term_language',
    ),
  );
}

/**
 * Implementation of hook_views_handlers().
 */
function i18ntaxonomy_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'i18ntaxonomy') . '/includes',
    ),
    'handlers' => array(
      // field handlers
      'i18ntaxonomy_handler_field_term_language' => array(
        'parent' => 'views_handler_field_taxonomy',
      ),
      // filters
      'i18ntaxonomy_handler_filter_term_language' => array(
        'parent' => 'views_handler_filter_in_operator',
      ),
      // argument handlers
      'i18ntaxonomy_handler_argument_term_language' => array(
        'parent' => 'views_handler_argument',
      ),
    ),
  );
}
