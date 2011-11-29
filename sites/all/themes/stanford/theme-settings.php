<?php
/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   array An array of saved settings for this theme.
 * @return
 *   array A form array.
 */
function phptemplate_settings($saved_settings) {
  /*
   * The default values for the theme variables. Make sure $defaults exactly
   * matches the $defaults in the template.php file.
   */
  $defaults = array(
    'nav_classes' => '',
	'layout_classes' => '',
	'icon_classes' => '',
	'header_classes' => '',
	'banner_classes' => '',
	'banner_image_path' => '',
  );

  // Merge the saved variables and their default values
  $settings = array_merge($defaults, $saved_settings);

  // Create the form widgets using Forms API
  
  // Page Layout
  $form['layout_container'] = array(
    '#type' => 'fieldset',
    '#title' => t('Page Layout'),
    '#description' => t('Use these settings to change the layout of the page.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  
  $form['layout_container']['layout_classes'] = array(
    '#type'          => 'radios',
    '#title'         => t('Select page layout'),
    '#default_value' => $settings['layout_classes'],
    '#options'       => array(
      '' => t('Fixed 960 px width, with standard 159 px sidebar(s) - <strong><em>Default</em></strong>'),
	  'news ' => t('Fixed 960 px width, with wide 279px sidebar'),
      'wide ' => t('Flexible 100% width, with flexible 16.5% sidebar(s)'),
    ),
  );
  
  $form['layout_container']['header_classes'] = array(
    '#type'          => 'radios',
    '#title'         => t('Select banner site title area to the right of Stanford signature'),
    '#default_value' => $settings['header_classes'],
    '#options'       => array(
      '' => t('Standard 450 px banner site title area - <strong><em>Default</em></strong>'),
      'sitename ' => t('Long 675 px banner site title area - requires all blocks (e.g., search block) to be removed from the header region'),
    ),
  );
  
  $form['layout_container']['icon_classes'] = array(
    '#type'          => 'radios',
    '#title'         => t('Select sidebar header image display'),
    '#default_value' => $settings['icon_classes'],
    '#options'       => array(
      '' => t('No sidebar header images - <strong><em>Default</em></strong>'),
	  'icon ' => t('Use sidebar header images'),
    ),
  );
    
  // Top Link Navigation Layout
  $form['nav_container'] = array(
    '#type' => 'fieldset',
    '#title' => t('Top Link Navigation Layout'),
    '#description' => t('Use these settings to change the appearance and functionality of top link navigation.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  
  $form['nav_container']['nav_classes'] = array(
    '#type'          => 'radios',
    '#title'         => t('Select top link navigation style'),
    '#default_value' => $settings['nav_classes'],
    '#options'       => array(
      '' => t('Stanford Modern style - <strong><em>Default</em></strong>'),
      'ucomm ' => t('Stanford Homepage style (restricted or grandfathered)'),
    ),
  );
  
  // Front Page Banner Graphic
  $form['banner_container'] = array(
    '#type' => 'fieldset',
    '#title' => t('Front Page Image'),
    '#description' => t('Use these settings to add an image to the front page.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  
  $form['banner_container']['banner_classes'] = array(
    '#type'          => 'radios',
    '#title'         => t('Select image display'),
    '#default_value' => $settings['banner_classes'],
    '#options'       => array(
      '' => t('No image - <strong><em>Default</em></strong>'),
	  'banner ' => t('Use image below:<br /><table class="gold-header" style="margin: 15px; width: auto;">
	  <tr><th style="padding-right: 30px;">Front page layout</th><th>Image dimensions</th></tr>
	  <tr><td style="padding-right: 30px;">Fixed 960 px width, with standard 159 px sidebar(s)</td><td>754 x 160 px</td></tr>
	  <tr><td style="padding-right: 30px;">Fixed 960 px width, with wide 279 px sidebar</td><td>635 x 160 px</td></tr>
	  <tr><td style="padding-right: 30px;">Fixed 960 px width, with no sidebars</td><td>940 x 160 px</td></tr>
	  </table>'),
    ),
  );
  
  // This ensures that a 'files' directory exists if it hasn't
  // already been been created.
  file_check_directory(file_directory_path(), 
    FILE_CREATE_DIRECTORY, 'file_directory_path');

  // Check for a freshly uploaded header image, save it to the
  // filesystem, and grab its full path for later use.
  if ($file = file_save_upload('banner_image',
      array('file_validate_is_image' => array()))) {
    $parts = pathinfo($file->filename);
    $filename = 'banner.'. $parts['extension'];
    if (file_copy($file, $filename, FILE_EXISTS_REPLACE)) {
      $settings['banner_image_path'] = $file->filepath;
    }
  }

  // Define the settings-related FormAPI elements.
  $form['banner_container']['banner_image'] = array(
    '#type' => 'file',
    '#title' => t('Upload banner graphic in .jpg, .gif, or .png format'),
    '#maxlength' => 40,
  );
  $form['banner_container']['banner_image_path'] = array(
    '#type' => 'value',
    '#value' => !empty($settings['banner_image_path']) ?
      $settings['banner_image_path'] : '',
  );
  if (!empty($settings['banner_image_path'])) {
    $form['banner_container']['banner_image_preview'] = array(
      '#type' => 'markup',
      '#value' => !empty($settings['banner_image_path']) ? 
          theme('image', $settings['banner_image_path']) : '',
    );
  }

  // Return the additional form widgets
  return $form;
}
?>