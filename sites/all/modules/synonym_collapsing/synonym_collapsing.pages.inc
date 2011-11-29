<?php

/**
 * Helper function for autocompletion
 */
function synonym_collapsing_autocomplete($vid, $string = '') {
  // The user enters a comma-separated list of tags. We only autocomplete the last tag.
  $array = drupal_explode_tags($string);

  $vids = array_keys(variable_get('synonym_collapsing_vocabularies', array()));
  $synonym_collapsing = in_array($vid, $vids);

  // Fetch last tag
  $last_string = trim(array_pop($array));
  $matches = array();
  if ($last_string != '') {
    $result = db_query_range(db_rewrite_sql("SELECT t.tid, t.name FROM {term_data} t WHERE t.vid = %d AND LOWER(t.name) LIKE LOWER('%%%s%%')", 't', 'tid'), $vid, $last_string, 0, 10);
    $prefix = count($array) ? implode(', ', $array) .', ' : '';
    $tags = array();
    
    if ($synonym_collapsing) {
      $result = db_query(db_rewrite_sql("SELECT * FROM {term_synonym} s, {term_data} t WHERE t.tid = s.tid AND LOWER(s.name) LIKE LOWER('%%%s%%')"), $last_string);
      while ($base = db_fetch_object($result)) {
        if (is_object($base)) {
          $tags[$base->tid] = $base;
        }
      }
    }

    while ($tag = db_fetch_object($result)) {
      $tags[] = $tag;
    }
    foreach ($tags as $tag) {
      $n = $tag->name;
      // Commas and quotes in terms are special cases, so encode 'em.
      if (strpos($tag->name, ',') !== FALSE || strpos($tag->name, '"') !== FALSE) {
        $n = '"'. str_replace('"', '""', $tag->name) .'"';
      }
      else {
        $matches[$prefix . $n] = check_plain($tag->name);
      }
    }
  }

  drupal_json($matches);
}