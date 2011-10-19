<?php

function synonym_collapsing_admin() {
  $form = array();
  $options = array();
  $vocabs = taxonomy_get_vocabularies();
  foreach ($vocabs as $vocab) {
    if ($vocab->tags) {
      $options[$vocab->vid] = $vocab->name;
    }
  }
  $form['synonym_collapsing_vocabularies'] = array(
    '#title' => t('Synonym collapsing vocabularies'),
    '#description' => t('The vocabularies in which synonyms should collapse.'),
    '#type' => 'select',
    '#options' => count($options > 0) ? $options : t('No vocabularies currently available.'),
    '#disabled' => count($options > 0) ? FALSE : TRUE,
    '#default_value' => variable_get('synonym_collapsing_vocabularies', array()),
    '#multiple' => TRUE,
  );
  return system_settings_form($form);
}