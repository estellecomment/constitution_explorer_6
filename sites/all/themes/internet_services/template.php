<?php
/**
 * @file
 * Contains theme override functions and preprocess functions for the theme.
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. You can add new regions for block content, modify
 *   or override Drupal's theme functions, intercept or make additional
 *   variables available to your theme, and create custom PHP logic. For more
 *   information, please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/theme-guide
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   The Drupal theme system uses special theme functions to generate HTML
 *   output automatically. Often we wish to customize this HTML output. To do
 *   this, we have to override the theme function. You have to first find the
 *   theme function that generates the output, and then "catch" it and modify it
 *   here. The easiest way to do it is to copy the original function in its
 *   entirety and paste it here, changing the prefix from theme_ to internet_services_.
 *   For example:
 *
 *     original: theme_breadcrumb()
 *     theme override: internet_services_breadcrumb()
 *
 *   where internet_services is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_breadcrumb() function.
 *
 *   If you would like to override any of the theme functions used in Zen core,
 *   you should first look at how Zen core implements those functions:
 *     theme_breadcrumbs()      in zen/template.php
 *     theme_menu_item_link()   in zen/template.php
 *     theme_menu_local_tasks() in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called template suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node-forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and template suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440
 *   and http://drupal.org/node/190815#template-suggestions
 */


/**
 * Implementation of HOOK_theme().
 */
function internet_services_theme(&$existing, $type, $theme, $path) {
  $hooks = zen_theme($existing, $type, $theme, $path);
  // Add your theme hooks like this:
  /*
  $hooks['hook_name_here'] = array( // Details go here );
  */
  // @TODO: Needs detailed comments. Patches welcome!
  return $hooks;
}

/**
 * Override or insert variables into all templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered (name of the .tpl.php file.)
 */
/* -- Delete this line if you want to use this function
function internet_services_preprocess(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
function internet_services_preprocess_page(&$vars, $hook) {
  $directory = drupal_get_path('theme', 'internet_services') . '/css/';
  $query_string = '?'. substr(variable_get('css_js_query_string', '0'), 0, 1);
  $base_path = base_path() . $directory;
  

  // Add layout stylesheets manually instead of via its .info file.
  switch (theme_get_setting('zen_layout')) {
    case 'zen-columns-liquid':
      $stylesheet = 'layout-liquid.css';
      break;
    case 'zen-columns-fluid':
      $stylesheet = 'layout-fluid.css';
      break;
    case 'zen-columns-fixed':
      $stylesheet = 'layout-fixed.css';
      break;
  }
  drupal_add_css($directory . $stylesheet, 'theme', 'all');

  // Regenerate the stylesheets.
  $vars['css'] = drupal_add_css();
  $vars['styles'] = drupal_get_css();

  // Add IE styles.
  $vars['styles'] .= '<!--[if IE]><link type="text/css" rel="stylesheet" media="all" href="' . $base_path . 'ie.css' . $query_string . '" /><![endif]-->' . "\n";
  $vars['styles'] .= '<!--[[if lte IE 6]><link type="text/css" rel="stylesheet" media="all" href="' . $base_path . 'ie6.css' . $query_string . '" /><![endif]-->' . "\n";

  // #1083694: Manually add custom.css file.
  if (file_exists("$directory/custom.css")) {
    $vars['styles'] .= '<link type="text/css" rel="stylesheet" media="all" href="' . $base_path . 'custom.css' . $query_string . '" />' . "\n";
  }
  
  //ESTELLE - reformat the breadcrumbs properly if possible. 
  // Note : this in only for the page breadcrumbs, not nodes.
  $separator = theme_get_setting('zen_breadcrumb_separator');
  $old_crumbs = $vars['breadcrumb'];
  if (substr($old_crumbs, 0, 24) ==  '<div class="breadcrumb">' && substr($old_crumbs,-6,6) == '</div>'){
      $old_crumbs = substr($old_crumbs, 24, strlen($old_crumbs)-30);
      $crumbs = explode($separator, $old_crumbs);
      $new_crumbs = '<div class="breadcrumb"><div class="breadcrumb-element">' . $crumbs[0] . '</div>';
      $crumbs = array_slice($crumbs, 1);
      foreach($crumbs as $crumb){
          if (strlen($crumb)>0){
            $new_crumbs = $new_crumbs . '<div class="breadcrumb-element">' . $separator . $crumb . '</div>';
          }          
      }
      $new_crumbs = $new_crumbs . '</div>';
      $vars['breadcrumb'] = $new_crumbs;
  }
  // ESTELLE - add base URL for Facebook tags
  global $base_url;
  $vars['base_url'] = $base_url;
  
  // ESTELLE - add direction in the classes
  $vars['classes_array'][] = $vars['language']->dir ;
  
  // Get the depth
  if ($vars['node'] && $vars['node']->type == "book"){
    $vars['depth'] = $vars['node']->book['depth'];
  }
  // Find the vid for the Country vocabulary
  $vocabularies = taxonomy_get_vocabularies();
  if (!empty($vocabularies)){
     foreach($vocabularies as $vocabulary) {
         if ($vocabulary->name == "Country") {
             $countriesvid = $vocabulary->vid;
         }
     }
  }
  // Get the country, to display in the facebook open graph tags
  if ($countriesvid && $vars['node'] && $vars['node']->taxonomy){
    foreach ($vars['node']->taxonomy as $oneterm){
      $iscontry = $oneterm;
      if($oneterm->vid == $countriesvid){
          $vars['country'] = $oneterm->name;
          break;
      }
    }
  }
    
}

/**
 * Override or insert variables into the node templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
function internet_services_preprocess_node(&$vars, $hook) {
  // Reset node links without class "inline", sync with Drupal 7.x.
  $vars['links'] = !empty($vars['node']->links) ? theme('links', $vars['node']->links, array('class' => 'links')) : '';
  
  // ESTELLE
  // add breadcrumbs if book
  $with_home = false; //include "Home" in the breadcrumbs?
  if ($vars['type'] == 'book'){
    $base_path = base_path();
    $book_link = $vars['book'];
    $trail = build_active_trail($book_link, $with_home);
    $breadcrumb = '<div class="breadcrumb">';
    $separator = theme_get_setting('zen_breadcrumb_separator');
    foreach($trail as $link){
        if ($link['href'] == '<front>'){
            $node_url = $base_path;
        }else{
            $node_url = $base_path . $link['href'];
        }
        if($node_url != $vars['node_url']){
            $breadcrumb = $breadcrumb . '<div class="breadcrumb-element"><a href="' . $node_url . '">' . $link['title'] . '</a>' . $separator . '</div>';
        }else{
            break;
        }
    }
    $vars['breadcrumb'] = $breadcrumb . '</div>';
  }
  
  // ESTELLE - add comment link if needed
  //$links = comment_links($vars['node'], $vars['teaser']); // edit, delete, reply -> not add!
  $links = $vars['node']->links;
  $link = $links['comment_add'];
  $linkthemed = theme('links', array(0=>$link), array('class' => 'links'));
  $vars['commentlink'] = $linkthemed;
  //<li class="comment_add"><a href="/constitution_explorer_6/comment/reply/179#comment-form" title="Share your thoughts and opinions related to this posting.">Add new comment</a></li>
  
  // expose facebook Like button if needed
  if ($vars['page']){
      $vars['like_button'] = $node->like_button;
  }
}

/** ESTELLE
 * Override or insert variables into the comment templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
function internet_services_preprocess_comment_wrapper(&$vars, $hook) {
  $links = $vars['node']->links;
  $link = $links['comment_add'];
  $linkthemed = theme('links', array('comment_add'=>$link), array('class' => 'links inline'));
  $vars['addcommentlink'] = $linkthemed;
     
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function internet_services_preprocess_block(&$vars, $hook) {
  $vars['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Preprocess variables for region.tpl.php
 *
 * Prepare the values passed to the theme_region function to be passed into a
 * pluggable template engine.
 *
 * @see region.tpl.php
 */
function internet_services_preprocess_region(&$vars, $hook) {
  // Create the $content variable that templates expect.
  $vars['content'] = $vars['elements']['#children'];
  $vars['region'] = $vars['elements']['#region'];

  // Setup the default classes.
  $vars['classes_array'] = array('region', 'region-' . str_replace('_', '-', $vars['region']));

  // Sidebar regions get a couple extra classes.
  if (strpos($vars['region'], 'sidebar_') === 0) {
    $vars['classes_array'][] = 'column';
    $vars['classes_array'][] = 'sidebar';
    $vars['template_files'][] = 'region-sidebar';
  }
  else if (strpos($vars['region'], 'triptych_') === 0) {
    $vars['classes_array'][] = 'column';
    $vars['classes_array'][] = 'triptych';
    $vars['template_files'][] = 'region-triptych';
  }
  else if (strpos($vars['region'], 'footer_') === 0) {
    $vars['classes_array'][] = 'column';
    $vars['classes_array'][] = 'footer';
    $vars['template_files'][] = 'region-footer';
  }
}

/**
 * Override or insert variables into templates after preprocess functions have run.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered.
 */
function internet_services_process(&$vars, $hook) {
  // Only override for region sidebar_*, triptych_* or footer_*.
  if (strpos($vars['region'], 'sidebar_') === 0 || strpos($vars['region'], 'triptych_') === 0 || strpos($vars['region'], 'footer_') === 0) {
    $vars['classes'] = implode(' ', $vars['classes_array']);
  }
}

/************
 * Estelle : little extra piece to change the text of the "Apply" button in views, and localize it
 */
function internet_services_preprocess_views_exposed_form(&$vars, $hook)
{
         // only alter the required form based on id
            if ($vars['form']['#id'] == 'views-exposed-form-Search-page-1') {
              // Change the text on the submit button
              $vars['form']['submit']['#value'] = t('Search');
              // Rebuild the rendered version (submit button, rest remains unchanged)
              unset($vars['form']['submit']['#printed']);
              $vars['button'] = drupal_render($vars['form']['submit']);
         }
}


/**
 * ESTELLE
 * Build an active trail to show in the breadcrumb.
 * code copied from book_build_active_trail in book.module
 */
function build_active_trail($book_link, $with_home) {
 // static $trail; // we want it recomputed everytime.

//  if (!isset($trail)) {
    $trail = array();
    if ($with_home){
        $trail[] = array('title' => t('Home'), 'href' => '<front>', 'localized_options' => array());
    }
    
    $tree = menu_tree_all_data($book_link['menu_name'], $book_link);
    $curr = array_shift($tree);

    while ($curr) {
      if ($curr['link']['href'] == $book_link['href']) {
        $trail[] = $curr['link'];
        $curr = FALSE;
      }
      else {
        if ($curr['below'] && $curr['link']['in_active_trail']) {
          $trail[] = $curr['link'];
          $tree = $curr['below'];
        }
        $curr = array_shift($tree);
      }
    }
  //}
  return $trail;
}


/******************************
 * END BOOK DISPLAY STUFF
 * ********************************/

/****************************
* Display taxonomy terms broken out by vocabulary - Estelle
 * code from : http://drupal.org/node/133223 (with edits for th html tags)
 ****************************/
function internet_services_print_terms($node, $vname = NULL, $labels = TRUE, $separator = ' - ') {
     $output = '';
     $vocabularies = taxonomy_get_vocabularies();
     if (!empty($vocabularies)){
        if ($vname) { //checks to see if you've passed a number with vid, prints just that vid
           foreach($vocabularies as $vocabulary) {
               if ($vocabulary->name == $vname) {
                   $terms = taxonomy_node_get_terms_by_vocabulary($node, $vocabulary->vid);
                   $output .= render_vocabulary($terms, $vocabulary, $labels, $separator);
               }
           }
        }
        else {
            $output = '<div class="taxonomy">';
            foreach($vocabularies as $vocabulary) {
                $terms = taxonomy_node_get_terms_by_vocabulary($node, $vocabulary->vid);
                $output .= render_vocabulary($terms, $vocabulary, $labels, $separator);
            }
            $output .= '</div>';
        }
     }
     return $output;
}

// ESTELLE  - render the tags to display in nodes
function render_vocabulary($terms, $vocabulary, $labels, $separator){
  $output = '';
  if ($terms) {
     $links = array();
     $output .= '<div class="field field-name-field-' . strtolower($vocabulary->name) .' field-type-taxonomy-term-reference field-label-inline">';
     if ($labels){
         $output .= '<div class="field-label field-label-inline-first">' . t($vocabulary->name) . ' : </div>';
     }
     $output .= '<div class="field-items">';
     foreach ($terms as $term) {
        $links[] = '<div class="field-item">' . l($term->name, taxonomy_term_path($term), array('rel' => 'tag', 'title' => strip_tags($term->description))) .'</div>';
        /*$links[] = '<div class="field-item">' . l($term->name, 'search',      
           array( 'query' => 'body=&body_1=&tid=' . str_replace(" ","+",$term->name) , 'attributes' => array('title' => t('See articles with this term'))) //'class' => 'xxx',
           ) .'</div>';*/
     }
     $output .= implode($separator, $links);
     $output .= '</div>';//terms
     $output .= '</div>';// vocabulary
  }   
  return $output;
}


function internet_services_preprocess_flat_book_node_export_html(&$vars, $hook) {
    if ($vars['node']->book['has_children'] == 0){
       $vars['reportlink'] = '<div class="report links"><a href="' . base_path() . 'contribute">Report an error</a></div>';
    }
    $vars['singlenodelink'] = l($vars['node']->title, "singlenode/" . $vars['node']->nid); 
    if (user_access('post comments')){
	$vars['nodecommentlink'] = l(t("Add a comment"), "singlenode/" . $vars['node']->nid, array("attributes"=>array("class"=>"comment-add")));
    }
}

/* ESTELLE - get children nodes of a book node - not used any more.*/
/*function book_children_nodes($book_link){
  $flat = book_get_flat_menu($book_link);

  $children_nid = array();

  if ($book_link['has_children']) {
    // Walk through the array until we find the current page.
    do {
      $link = array_shift($flat);
    }
    while ($link && ($link['mlid'] != $book_link['mlid']));
    // Continue though the array and collect the links whose parent is this page.
    while (($link = array_shift($flat)) && $link['plid'] == $book_link['mlid']) {
      // get children's nid
      $href = $link['href']; // we count on href being "node/XX" otherwise breaks!
      preg_match ( "/([0-9]+)$/", $href , $matches);
      if (!empty ($matches[0])){
          $nodenid = intval($matches[0]);
          $children_nid[] = $nodenid;
      }else{
          // problem! nid not found
      }
    }
  }
  
  // load the nodes
  // $children = node_load_multiple($children_nid);// doesn't exist in drupal 6!
  //$themed_children = node_view_multiple($children, 'full'); // doesn't exist in drupal 6!
  $themed_children = array();
  foreach($children_nid as $nid){
      $child = node_load($nid);
      $themed_child = node_view($child); 
      $themed_children[] = $themed_child;
  }
  // prep nodes for theming
   return $themed_children; 
}*/

function internet_services_preprocess_book_navigation(&$vars, $hook){
    //ESTELLE
    // replace the "/sitename/?q=node/123" by "/sitename/?q=singlenode/123" 
    // This avoids returning to the full page (flatbook) view of the constitution 
    // when you are in a singlenode view.
    $parent_node_url = $vars['parent_url'];
    $parent_singlenode_url = str_replace("node", "singlenode", $parent_node_url);
    $vars['parent_url'] = $parent_singlenode_url;
    
    // modify children's links to singlenode
    // Note : we don't know if it's clean or dirty URLs, but l() knows
    $link_node = l("", "node");
    $link_node = str_replace("\"></a>", "", $link_node);
    $link_node = str_replace("<a ", "", $link_node); // href="/constitution_explorer_6_2/?q=node
    $link_singlenode = str_replace('node', 'singlenode', $link_node); //href="/constitution_explorer_6_2/?q=singlenode
    $vars['tree'] = str_replace($link_node, $link_singlenode, $vars['tree']);
    
    // add link to go back to full text (flatbook) view
    $fullview_link = l("Back to full text", $vars['book_link']['href']);
    $vars['fullview_link'] = $fullview_link;
}

