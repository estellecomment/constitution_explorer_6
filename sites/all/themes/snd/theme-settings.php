<?php
// $Id: theme-settings.php,v 1.1 2009/06/06 18:19:44 giorgio79 Exp $

/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   An array of saved settings for this theme.
 * @return
 *   A form array.
 */
function snd_settings($saved_settings) {

  // Get the default values from the .info file.
  $themes = list_themes();
  $defaults = $themes['snd']->info['settings'];

  // Merge the saved variables and their default values.
  $settings = array_merge($defaults, $saved_settings);

  /*
   * Create the form using Forms API: http://api.drupal.org/api/6
   */
  $form = array();
  /* -- Delete this line if you want to use this setting
  $form['subtheme_example'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use this sample setting'),
    '#default_value' => $settings['subtheme_example'],
    '#description'   => t("This option doesn't do anything; it's just an example."),
  );
  // */

  // Add the base theme's settings.
  include_once './' . drupal_get_path('theme', 'zen') . '/theme-settings.php';
  $form += zen_settings($saved_settings, $defaults);
  $form['breadcrumb']['zen_breadcrumb_leading'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Prepend a separator to the beginning of the breadcrumb'),
    '#default_value' => $settings['zen_breadcrumb_leading'],
    '#description'   => t(''),
  );

  // Remove some of the base theme's settings.
  unset($form['themedev']['zen_layout']); // We don't need to select the base stylesheet.

  // Return the form
  return $form;
}
