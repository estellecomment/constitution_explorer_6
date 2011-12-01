// $Id$

-SUMMARY-

This module provides URL rewrites for integrating Drupal with a Reverse Proxy service. It was 
developed by the Stanford University Open Source Lab for integrating with the Virtual Host Proxy
Service provided by Stanford University ITS (aka, "Vanity URL").

-CAVEATS-

There is no warranty, expressed nor implied, included with the Reverse Proxy module.

This module CAN SERIOUSLY BREAK YOUR SITE. Do not enable this for the first time on a live, 
production website. Best practices would dictate that you install and enable Reverse Proxy when your
site is in early developmental stages. 

Version 6.x-2.x of this module is intended to be installed and enabled on fresh Drupal installs, 
ideally using the Collaboration Tools Installer. If you are using an earlier version of the Reverse 
Proxy Module on an existing site and it is working, you do not need to upgrade.

Additionally, this module requires that your Drupal site be installed on Leland servers 
(i.e., in AFS space), as it relies on some custom commands for determining proxy information.

-CONFIGURATION-

The below instructions assume that you have /afs/ir/group/yourgroup/cgi-bin/drupal as your Drupal 
installation directory.

1) Go to http://vanityurl.stanford.edu to set up a virtual host proxy (https://yourgroup.stanford.edu) 
to point to your Drupal directory:
  https://www.stanford.edu/group/yourgroup/cgi-bin/drupal
2) If you want to use the WebAuth module on your site (you do), you MUST request that the virtual 
host proxy use SSL (i.e., https://yourgroup.stanford.edu) in the "Additional Configuration" section
3) In /afs/ir/group/yourgroup/cgi-bin/drupal.htaccess, set the RewriteBase variable to: 
	RewriteBase /group/yourgroup/cgi-bin/drupal
	(Note that this is set by default on sites installed with the Collaboration Tools Installer)
4) Enable the Reverse Proxy module
  NOTE: Simply enabling the module does not do anything immediately. You must go on to Step 5.
5) Go to admin/settings/reverse_proxy and verify that the information is correct. If it is, click
  "Save configuration"
6) You will be redirected to https://yourgroup.stanford.edu/user, and you will need to log in again.
7) (optional) Create a .htaccess file in /afs/ir/group/yourgroup/WWW/ with the following directives:
	RewriteEngine on
	RewriteRule (.*) http://yourgroup.stanford.edu/$1 [R=301,L]
  (Note: do not complete step 7 if you have files living in your WWW directory that you want to be 
  accessible via the web.)
8) (also optional)  Add the following lines to your Drupal .htaccess file to only allow the site to
   be accessed using the Vanity URL:
  # Force loading through the vanity URL
  RewriteCond %{HTTP:X-Forwarded-Host} !^yourgroup.stanford.edu*
  RewriteCond %{REQUEST_URI} !webauth
  RewriteRule (.*) https://yourgroup.stanford.edu/$1 [L,QSA]


