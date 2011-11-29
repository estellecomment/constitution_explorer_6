<?php	
// $Id: wa_login.inc 1657 2009-10-29 18:22:59Z ksharp $
	
	// once the user has logged in through weblogin, write some 
	// webauth data about the user into a PHP Session

        global $base_url, $cookie_domain, $conf;
        
        // get the drupal base directory that was calculated when
	// this module was installed
	require('wa_base.inc');
	$my_base = $base;

	// Drupal's session key name is based on either the $cookie_domain or $base_url
	// in settings.php. If neither is set, use the base_url calculated on installation
	chdir("..");
	require_once ('./includes/common.inc');
	require_once ('./includes/bootstrap.inc');
	include_once ('./' . conf_path() . '/settings.php');

	// if the cookie domain isn't set, see if it's set in the php configuration
	if (!$cookie_domain) {
		$cookie_domain = ini_get('session.cookie_domain');
	}

	// neither is set, so use our pre-calculated base_url
	if (!$cookie_domain && !$base_url) {
		$base_url = $my_base;
	}

	// bootstrap enough of Drupal to get some info we need
        drupal_unset_globals();
        conf_init();
        require_once variable_get('cache_inc', './includes/cache.inc');
        require_once ('./includes/database.inc');
        db_set_active();

	// load Drupal's session handler so we can get a session_name to mimic
	require_once variable_get('session_inc','./includes/session.inc');
	session_set_save_handler('sess_open', 'sess_close', 'sess_read', 'sess_write', 'sess_destroy_sid', 'sess_gc');
	session_start();

	$conf = variable_init(isset($conf) ? $conf : array());

        // find the webauth module
        $fname = drupal_get_filename('module','webauth');
        if (strlen($fname) > 0) {
           $lastsl = strrpos($fname,'/');
           if ($lastsl) {
	      $fname = substr($fname,0,$lastsl);
	   }
	}
        $fname = $fname . '/wa_session.inc';
        require($fname);

        // initialize our array
        $wa_session = array();

	// store the current time and the webauth_at cookie in an array
	$wa_data = array('wa_new' => TRUE, 'wa_time' => time(), 'wa_at' => md5($_COOKIE['webauth_at']));
	
	// store any other webauth data, such as WEBAUTH_USER or WEBAUTH_EMAIL in the array
	foreach ($_SERVER as $key => $value) {
		if (strtoupper(substr($key,0,8)) == 'WEBAUTH_') {
			$wa_data['wa_'.strtolower(substr($key,8))] = $value;
		}
	}

	// store the array in our session
	$wa_session['wa_data'] = $wa_data;	

	// if drupal is running under a virtual host name via Stanford proxy service, then weblogin may have
	// replaced the virutal host name with the real one. if so, drupal won't be able to see the webauth
	// app cookie (webauth_at). A session  parameter set by webauth.module will tell us if this is so
	// and we can temporarily encrypt and store the cookie value for drupal to see when we return.
	$wa_xhost = variable_get('webauth_xhost',NULL);
	if ($wa_xhost != NULL) {
		$pub_key = variable_get('webauth_pubkey','');
		if (strpos($pub_key,'CERTIFICATE') > -1) {
			$pub_key = openssl_pkey_get_public($pub_key);
		}
		if (openssl_public_encrypt($_COOKIE['webauth_at'],$at_out,$pub_key)) {
			$wa_session['wa_crypt'] = rawurlencode($at_out);
		}
	}

        // store our data in our webauth session
	wmdSessionCreate(session_name(), $wa_session);

	// see where the original drupal request was going
	// make sure we always return to drupal's user/login page (which we are overriding)
	// and let that code send us on to our original destination
	
	$dest_str = NULL;
	if ($_GET['wa_dest']) {
		// if a return destination is set as a url parameter, use it
		$dest_str = $_GET['wa_dest'];
	} else {
		// otherwise see if it is set as a system variable or in a return address cookie
		$dest_str = variable_get('webauth_destination', '');
		$postSess = session_name() .'_WMDPOST';
		if (isset($_COOKIE[$postSess])) {
			$dest_str = $_COOKIE[$postSess];
		}
	}

	$newquery = 'q=user/login';
	if (!empty($dest_str)) $newquery .= '&destination='.drupal_urlencode($dest_str);
	
	// add back on any other query parameters in the original request
	foreach ($_GET as $key => $value) {
		if (!($key === 'q' || $key === 'destination')) {
			$newquery = $newquery . '&' . $key . '=' . drupal_urlencode($value);
		} 
	}
	
	// redirect browser back to drupal	
	header('Location: '.$my_base.'/?'.$newquery);

?>
