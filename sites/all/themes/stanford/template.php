<?php
/*
 * Initialize theme settings
 */
if (is_null(theme_get_setting('nav_classes'))) {
  global $theme_key;

  /*
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the theme-settings.php file.
   */
  $defaults = array(
    'nav_classes' => '',
	'layout_classes' => '',
	'icon_classes' => '',
	'header_classes' => '',
	'banner_classes' => '',
	'banner_image_path' => '',
  );

  // Get default theme settings.
  $settings = theme_get_settings($theme_key);
  // Don't save the toggle_node_info_ variables.
  if (module_exists('node')) {
    foreach (node_get_types() as $type => $name) {
      unset($settings['toggle_node_info_' . $type]);
    }
  }
  // Save default theme settings.
  variable_set(
    str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
    array_merge($defaults, $settings)
  );
  // Force refresh of Drupal internals.
  theme_get_setting('', TRUE);
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
 
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
// uncomment the next line to enable current page in the breadcrumb trail
    $breadcrumb[] = drupal_get_title();
    return '<div class="breadcrumb">'. implode(' » ', $breadcrumb) .'</div>';
  }
}

/**
 * Allow themable wrapping of all comments.
 */

function phptemplate_comment_wrapper($content, $node) {
  if (!$content || $node->type == 'forum') {
    return '<div id="comments">'. $content .'</div>';
  }
  else {
    return '<div id="comments"><h2 class="comments">'. t('Comments') .'</h2>'. $content .'</div>';
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */

function phptemplate_preprocess_page(&$vars) { 
 
  // first, proceed with a modifed version of the standard 
  // Drupal template suggestion calls 
  $i = 0; 
  $suggestions = array(); 
  $suggestion = 'page'; 
  while ($arg = arg($i++)) { 
    $suggestions[] = $suggestion .'-'. $arg; 
    if (!is_numeric($arg)) { 
      $suggestion .= '-'. $arg; 
    } 
  } 
  // next, check for templates that use the path alias 
  if (module_exists('path')) { 
    $alias = drupal_get_path_alias(str_replace('/edit','',$_GET['q'])); 
    if ($alias != $_GET['q']) { 
      $template_filename = 'page'; 
      foreach (explode('/', $alias) as $path_part) { 
        $template_filename = $template_filename . '-' . $path_part; 
        $suggestions[] = $template_filename; 
      } 
    } 
    $vars['template_files'] = $suggestions; 
  } // end path alias template check 
  if ($suggestions) { 
    $vars['template_files'] = $suggestions; 
  }
  if (isset($vars['node'])) {
    // Add template naming suggestion. It should alway use hyphens.
    // If node type is "custom_news", it will pickup "page-custom-news.tpl.php".
    $vars['template_files'][] = 'page-'. str_replace('_', '-', $vars['node']->type);
  }
  $vars['tabs2'] = menu_secondary_local_tasks();
    // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }
  
  // Start Zen Preprocess Code
  
  // Add conditional stylesheets.
   if (!module_exists('conditional_styles')) {
     $vars['styles'] .= $vars['conditional_styles'] = variable_get('conditional_styles_' . $GLOBALS['theme'], '');
   }

   // Classes for body element. Allows advanced theming based on context
   // (home page, node of certain type, etc.)
   $classes = split(' ', $vars['body_classes']);
   // Remove the mostly useless page-ARG0 class.
   if ($index =
array_search(preg_replace('![^abcdefghijklmnopqrstuvwxyz0-9-_]+!s', '', 'page-'. drupal_strtolower(arg(0))), $classes)) {
     unset($classes[$index]);
   }
   if (!$vars['is_front']) {
     // Add unique class for each page.
     $path = drupal_get_path_alias($_GET['q']);
     $classes[] = zen_id_safe('page-' . $path);
     // Add unique class for each website section.
     list($section, ) = explode('/', $path, 2);
     if (arg(0) == 'node') {
       if (arg(1) == 'add') {
         $section = 'node-add';
       }
       elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) ==
'delete')) {
         $section = 'node-' . arg(2);
       }
     }
     $classes[] = zen_id_safe('section-' . $section);
   }
   if (theme_get_setting('zen_wireframes')) {
     $classes[] = 'with-wireframes'; // Optionally add the wireframes style.
   }
   $vars['body_classes_array'] = $classes;
   $vars['body_classes'] = implode(' ', $classes); // Concatenate with spaces.
   
   // End Zen Preprocess Code
} 

/**
  * Converts a string to a suitable html ID attribute.
  *
  * http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
  * valid ID attribute in HTML. This function:
  *
  * - Ensure an ID starts with an alpha character by optionally adding an 'id'.
  * - Replaces any character except alphanumeric characters with dashes.
  * - Converts entire string to lowercase.
  *
  * @param $string
  *   The string
  * @return
  *   The converted string
  */
function zen_id_safe($string) {
   // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
   $string = strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', $string));
   // If the first character is not a-z, add 'id' in front.
   if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
     $string = 'id' . $string;
   }
   return $string;
}


/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */

function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function phptemplate_comment_submitted($comment) {
  return t('by <strong>!username</strong> | !datetime',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
  return t('by <strong>!username</strong> | !datetime',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Adds even and odd classes to <li> tags in ul.menu lists
 */ 

function phptemplate_menu_item($link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL) {
  static $zebra = FALSE;
  $zebra = !$zebra;
  $class = ($menu ? 'expanded' : ($has_children ? 'collapsed' : 'leaf'));
  if (!empty($extra_class)) {
    $class .= ' '. $extra_class;
  }
  if ($in_active_trail) {
    $class .= ' active-trail';
  }
  if ($zebra) {
    $class .= ' even';
  }
  else {
    $class .= ' odd';
  }
  return '<li class="'. $class .'">'. $link . $menu ."</li>\n";
}
