<?php
// $Id: wa_check.inc 811 2008-06-18 23:13:46Z ksharp $

/**
 * @file
 * Returns webauth information to the Drupal webauth module
 * to verify that a webauth_at cookie is valid.
 */
	
	foreach ($_SERVER as $key => $value) {
		if (strtoupper(substr($key,0,8)) === 'WEBAUTH_') {
			$key2 = strtolower(substr($key,8));
			header('wa_'.$key2.': '.$value);
		}
	}
	echo '<html><head><title>wa_check</title></head>';
	echo '<body>';
	echo '</body></html>';
	
?>
