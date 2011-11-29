// $Id$

-SUMMARY-

This module provides URL rewrites for integrating Drupal with a Reverse Proxy service. It was 
developed by the Stanford University Open Source Lab for integrating with the Virtual Host Proxy
Service provided by Stanford University ITS.

-REQUIREMENTS-

The below instructions assume that you have /afs/ir/group/groupname/cgi-bin/drupal as your Drupal 
installation directory.

1) Set up a virtual host proxy (https://yourgroup.stanford.edu) to point to your Drupal directory:
	https://www.stanford.edu/group/yourgroup/cgi-bin/drupal
1a) NOTE: If you want to use the WebAuth module (or any https protocol), you MUST specify SSL 
    in the "Additional Configuration" section of the virtual host request form.
2) Enable the Reverse Proxy module
3) In /afs/ir/group/yourgroup/cgi-bin/drupal/.htaccess, set the RewriteBase variable to: 
	RewriteBase /group/yourgroup/cgi-bin/drupal
4) In /afs/ir/group/yourgroup/cgi-bin/drupal/sites/default/settings.php set:
	$base_url = 'https://yourgroup.stanford.edu'; // NO trailing slash!
5) (optional) Create a .htaccess file in /afs/ir/group/yourgroup/WWW/ with the following directives:
	RewriteEngine on
	RewriteRule (.*) https://yourgroup.stanford.edu/$1 [R=301,L]
6) (also optional) Add the following lines to your Drupal .htaccess file to only allow the site to
   be accessed using the Vanity URL:
  # Force loading through the vanity URL
  RewriteCond %{HTTP:X-Forwarded-Host} !^yourgroup.stanford.edu*
  RewriteCond %{REQUEST_URI} !webauth
  RewriteRule (.*) https://yourgroup.stanford.edu/$1 [L,QSA]
