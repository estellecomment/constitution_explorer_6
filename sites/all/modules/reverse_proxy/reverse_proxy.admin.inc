<?php
// $Id: reverse_proxy.admin.inc,v 1.28 2010-02-25 00:49:09 jbickar Exp $

/*
 * @file
 * Administration and diagnostic page for Reverse Proxy module.
 */
function reverse_proxy_settings_page() {
  drupal_add_css(drupal_get_path('module', 'reverse_proxy') .'/reverse_proxy.css');
  global $base_url;
  $su_rewritebase = $_SERVER['SCRIPT_NAME'];
  $su_rewritebase = preg_replace('/\/\w*\.php$/', '', $su_rewritebase);
  $rp = realpath('.');
  if ($_SERVER['HTTPS']) {
    $install_url = preg_replace('/\/afs\/ir/', 'https://www', $rp);
  }
  else {
    $install_url = preg_replace('/\/afs\/ir/', 'http://www', $rp);
  }
  $output .= '<ol class="reverse-proxy"><li>';
  $output .= t('You must have your Virtual Host Proxy set to point to your Drupal URL. The Reverse ');
  $output .= t('Proxy module has auto-detected your Drupal installation at:');
//  $output .= '<br /><br />';
  $output .= '<span class="messages">'. t("%install_url", array('%install_url' => $install_url)) .'</span>';
//  $output .= '<br /><br />';
  $output .= '</li>';
//  $output .= '<br /><br />';
  $output .= '<li>';
  $output .= t('The Reverse Proxy module has auto-populated the ') .'<strong>'. t('Virtual Host Proxy Address');
  $output .= '</strong>'. t(' drop-down list below with the address(es) of vanity URLs that point to your Drupal install directory. ');
  $output .= t('In 99% of the cases you will not need to change this from its default setting. ');
  $output .= t('Please verify that the Virtual Host Proxy that you have set up to point to your Drupal install is selected.');
  $output .= '</li>';
  $output .= '<li>';
  $output .= t('Once you check the %turn_on checkbox and click "Save Configuration", you will be redirected to the user login screen ', array('%turn_on' => 'Turn on Reverse Proxy URL rewriting'));
  $output .= t('at your Vanity URL, and you will have to log in again.');
  $output .= '</li>';
  $output .= drupal_get_form('reverse_proxy_settings_form');

  return $output;
}

/*
 * Configuration form for Reverse Proxy module.
 */
function reverse_proxy_settings_form() {
  $remctl_host_query = 'remctl tools proxy showhost ';
  $rp = realpath('.');
  $rp = preg_replace('/\/afs\/ir/', 'http://www', $rp);
  $remctl_host_query .= $rp;
  $proxy_host = shell_exec($remctl_host_query);
  $proxy_host = explode("\n", $proxy_host);
  if (!$proxy_host[0]) {  //if querying with http:// didn't return a value, try https://
    $remctl_host_query = 'remctl tools proxy showhost ';
    $rp = realpath('.');
    $rp = preg_replace('/\/afs\/ir/', 'https://www', $rp);
    $remctl_host_query .= $rp;
    $proxy_host = shell_exec($remctl_host_query);
    $proxy_host = explode("\n", $proxy_host);
  }
  else { //show them all if there's http and https hosts
    $remctl_host_query = 'remctl tools proxy showhost ';
    $rp = realpath('.');
    $rp = preg_replace('/\/afs\/ir/', 'https://www', $rp);
    $remctl_host_query .= $rp;
    $proxy_host_ssl = shell_exec($remctl_host_query);
    $proxy_host_ssl = explode("\n", $proxy_host_ssl);
    $proxy_host = array_merge($proxy_host, $proxy_host_ssl);
  }
  if (!$proxy_host[0]) { // set an error here if remctl query still fails to return a value with https://
    drupal_set_message(t('Your Virtual Host Proxy is not configured correctly.'), 'error');
    //not even going to bother showing the form if the proxy isn't properly configured
  }
  else {
    foreach ($proxy_host as $key => &$value) {
      $remctl_type_query = 'remctl tools proxy showtype '. $value;
      $redirect_proxy = shell_exec($remctl_type_query);
      $redirect_proxy = trim($redirect_proxy);
      if ($redirect_proxy == 'proxy') {
        $value = $value .'.stanford.edu';
      }
      else {
        unset($proxy_host[$key]);
      }
    }
    foreach ($proxy_host as &$value) {
      $value = preg_replace('/\s/', '', $value);
    }

    $proxy_host_flipped = array_flip($proxy_host);
    foreach ($proxy_host_flipped as $key => &$value) {
      $value = $key;
    }
    $form['reverse_proxy_settings'] = array(
      '#title' => t('Settings'),
      '#type' => 'fieldset',
      '#collapsible' => FALSE
    );

    $form['reverse_proxy_settings']['virtual_host_proxy'] = array(
      '#title' => t('Virtual Host Proxy Address'),
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#prefix' => '<div class="reverse-proxy-fieldset">',
      '#suffix' => '</div>'
    );
    $http = array('https://', 'http://');
    $http_flipped = array_flip($http);
    foreach ($http_flipped as $key => &$value) {
      $value = $key;
    }
    $form['reverse_proxy_settings']['virtual_host_proxy']['reverse_proxy_https'] = array(
      '#type' => 'select',
      '#options' => $http_flipped
    );

    $form['reverse_proxy_settings']['virtual_host_proxy']['reverse_proxy_proxy_host'] = array(
      '#type' => 'select',
      '#options' => $proxy_host_flipped
    );

    $form['reverse_proxy_settings']['reverse_proxy_on'] = array(
      '#title' => 'Turn on Reverse Proxy URL rewriting',
      '#type' => 'checkbox',
      '#description' => t('By checking this box you will rewrite your URLs from the form %pre_url to %post_url', array('%pre_url' => 'https://www.stanford.edu/group/yourgroup/cgi-bin/drupal', '%post_url' => 'https://yourgroup.stanford.edu/')),
      '#default_value' => variable_get('reverse_proxy_on', NULL)
    );

    $form['#submit'][] = 'reverse_proxy_edit_settings';
    $form['#cache'] = FALSE;

    return system_settings_form($form);
  }
}

/*
 * Make changes to .htaccess
 * DEPRECATED
 */
/*
function reverse_proxy_htaccess_edit($form, &$form_state) {
  $rb = shell_exec("grep -e '^[[:space:]]*[^#]RewriteBase' .htaccess");
  $rb = trim($rb);
  $su_rewritebase = $_SERVER['SCRIPT_NAME'];
  $su_rewritebase = preg_replace('/\/\w*\.php$/', '', $su_rewritebase);
  if ($rb == $su_rewritebase) {
    return;
  } elseif ($rb == '') {
    $htaccess = file_get_contents('.htaccess');
    if ($htaccess) {
      $htaccess_new = preg_replace('/\s\s#\sRewriteBase\s\/drupal/', '  RewriteBase '. $su_rewritebase .'/', $htaccess);
    } else {
      form_set_error(NULL, t('Unable to read contents of .htaccess file'));
    }
    $timestamp = time();
    $command = 'cp .htaccess .htaccess.backup-'. $timestamp;
    shell_exec($command);
    $htaccess = fopen('.htaccess', 'w');
    fwrite($htaccess, $htaccess_new);
    fclose($htaccess);
  } else {
    form_set_error(NULL, t('Oops! It looks like you already have a RewriteBase set in your .htaccess file.'));
  }
  return;
}*/

/*
 * Make sure a conflicting $base_url isn't set already, and that we can write to settings.php.
 */
function reverse_proxy_settings_form_validate($form, &$form_state) {
  global $base_url;
  $reverse_proxy_base_url = $form['reverse_proxy_settings']['reverse_proxy_proxy_host']['#value'];
  if ($base_url !== $reverse_proxy_base_url) {
    $bu = shell_exec("grep -e '^[[:space:]]*[^#]\$base_url'  sites/default/settings.php");
    if (!$bu) {
      $can_chmod = chmod('sites/default/settings.php', 0744);
      if (!$can_chmod) {
  //      $decperms = fileperms('sites/default/settings.php');
  //      $octalperms = sprintf("%o",$decperms);
  //      $perms=(substr($octalperms,3));
  //      dsm('file permissions are now '. $perms);
        form_set_error('', t('Cannot change permissions on settings.php. Please check that it is writeable and try again.'));
      }
    }
    else {
      form_set_error('', t('Your $base_url is already set to '. $bu .' in your settings.php. Please change it to '. $reverse_proxy_base_url));
    }
  }
}

/*
 * Write changes to settings.php and redirect to the path 'user' at the proxy host.
 */
function reverse_proxy_edit_settings($form, &$form_state) {
  if ($form_state['values']['reverse_proxy_on'] == '1') {
    $settings = file_get_contents('sites/default/settings.php');
    $pattern = '/#\s\$base_url\s=\s\'http:\/\/www.example.com\'\;\s\s\/\/\sNO\strailing\sslash\!/';
    $my_new_base = $form['reverse_proxy_settings']['virtual_host_proxy']['reverse_proxy_https']['#value'] . $form['reverse_proxy_settings']['virtual_host_proxy']['reverse_proxy_proxy_host']['#value'];
    $replacement = '$base_url = \''. $my_new_base ."';  // NO trailing slash!";
    $settings_new = preg_replace($pattern, $replacement, $settings);
    $timestamp = time();
    $settings_backup = 'settings.php.backup-'. $timestamp;
    variable_set('reverse_proxy_settings_backup', $settings_backup);
    $command = 'cp sites/default/settings.php sites/default/'. $settings_backup;
    shell_exec($command);
    chmod('sites/default/settings.php', 0744);
    $settings = fopen('sites/default/settings.php', 'w');
    fwrite($settings, $settings_new);
    fclose($settings);
    chmod('sites/default/settings.php', 0444);
    $form_state['redirect'] = ''. $my_new_base .'/user';
  }
  else {
    include_once('reverse_proxy.install');
    reverse_proxy_disable();
    $rp = realpath('.');
    $rp = preg_replace('/\/afs\/ir./', 'https://', $rp);
    $rp .= '/user';
    $form_state['redirect'] = $rp;
  }
}
