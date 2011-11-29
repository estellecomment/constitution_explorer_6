<?php

// $Id: wa_session.inc 1654 2009-10-27 18:02:32Z ksharp $

/**
 * @file
 * WMD's own session handler replaces Drupal's to avoid caching issues.
 * This file resides in the module directory, but is also copied to the 
 * main webauth directory for use by wa_login.php
 *
 */

function wmdWriteCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly) {

	// see if we've already made this cookie request
	static $cookieRequests = array();

	if (isset($cookieRequests[$name]) && $cookieRequests[$name] === $value) {
		return; // request already made
	} else {
		$cookieRequests[$name] = $value; // store it for testing
	}

	// write a cookie to the browser - check for PHP version to see whether to include $httpOnly value
	if (version_compare(PHP_VERSION,'5.2.0') === -1) {
		setcookie($name, $value, $expire, $path, $domain, $secure);
	} else {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}
}

function wmdSessionGetName($drupalSessionName) {
	
	// derive the wmd session name from the Drupal session name

	$wa_session_name = 'WMD_';
	if (empty($drupalSessionName) || strlen($drupalSessionName) < 4) {
		$wa_session_name .= 'SESSION';
	} else {
		$wa_session_name .= substr($drupalSessionName, 4);
	}
	return $wa_session_name;
}


function wmdSessionGetId($drupalSessionName) {

	// get the session id from the <session name> cookie, if it exists

	$wa_session_id = NULL;	
	$wa_session_name = wmdSessionGetName($drupalSessionName);

	if (isset($_COOKIE[$wa_session_name])) {
		$wa_session_id = $_COOKIE[$wa_session_name];
	}

	return $wa_session_id;
	
}


function wmdSessionRead($drupalSessionName) {

	// read our wmd session from the database if it exists, otherwise return an empty array

	$sid = wmdSessionGetId($drupalSessionName);
	$wa_session = array();
        
	if (!empty($sid)) {

		$result = db_query("SELECT session FROM {webauth_session} WHERE sid = '%s'", $sid);
		if ($result) {
 			$object = db_fetch_object($result);
			if ($object) {
				$wa_session = unserialize($object->session);
			}
		}
	}

	return $wa_session;

}

function wmdSessionwrite($drupalSessionName, $wa_session) {


	// write the session to the database, deleting the old one if it exists.

	$sid = wmdSessionGetId($drupalSessionName);
	if (!empty($sid) && isset($_SERVER['HTTPS'])) {
		$result = db_query("SELECT session FROM {webauth_session} WHERE sid = '%s'", $sid);
		if ($result) {
			$object = db_fetch_object($result);
			if ($object) {
				db_query("DELETE FROM {webauth_session} where sid = '%s'",$sid);
				db_query("INSERT INTO {webauth_session} (sid, timestamp, session) VALUES ('%s', '%d', '%s')", $sid, time(), serialize($wa_session));
			}
		}
	}

}

function wmdSessionCreate($drupalSessionName, $wa_session) {

	// create a new wmdsession, create its cookie, and write its record to the database
	
	global $cookie_domain;

	$wa_session_name = wmdSessionGetName($drupalSessionName);

	// first delete any existing wmd session
	$sid = wmdSessionGetId($drupalSessionName);
	if (!empty($sid)) {
		db_query("DELETE FROM {webauth_session} where sid = '%s'", $sid);
	}	
	// if this function is called during an HTTP (not HTTPS) request, or there is no session data, do nothing
	if (empty($wa_session) || !isset($_SERVER['HTTPS'])) return;

	// create a new session id and set the cookie
	$sid = uniqid('wmd',TRUE);
	wmdWriteCookie($wa_session_name, $sid, 0, '/', $cookie_domain, TRUE, TRUE);

	// write the session data to the database
	db_query("INSERT INTO {webauth_session} (sid, timestamp, session) VALUES ('%s', '%d', '%s')", $sid, time(), serialize($wa_session));

}


