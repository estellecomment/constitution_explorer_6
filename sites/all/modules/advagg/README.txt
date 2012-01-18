
----------------------------------
ADVANCED CSS/JS AGGREGATION MODULE
----------------------------------

CONTENTS OF THIS FILE
---------------------

 * Fast 404
 * Features & benefits
 * Configuration
 * Technical Details & Hooks
 * Single htaccess rules


FAST 404
--------

Assuming that this guide was followed:
http://2bits.com/drupal-planet/reducing-server-resource-utilization-busy-sites-implementing-fast-404s-drupal.html
and your having issues with Advanced CSS/JS Aggregation. Advagg works similar
to imagecache, thus we need to add in an exceptions for the directories that
advagg uses. Replace this if statement

    if (!strpos($_SERVER['QUERY_STRING'], 'imagecache')) {

with one like this:

    if (!strpos($_SERVER['QUERY_STRING'], 'imagecache') && !strpos($_SERVER['QUERY_STRING'], '/advagg_')) {

This will most likely be in your settings.php file.

If using Nginx make sure there is a rule similar to this in your configuration.
http://drupal.org/node/1116618#comment-4321724

If this is still an issue you can try setting the
"IP Address to send all asynchronous requests to" setting on the
admin/settings/advagg/config page to -1. This will use the hostname instead of
an IP address when making the http request.

If you are still having problems, open an issue on the advagg issue queue:
http://drupal.org/project/issues/advagg


FEATURES & BENEFITS
-------------------

Advanced CSS/JS Aggregation Core Module:
 * Imagecache style CSS/JS Aggregation. If the file doesn't exist it will be
   generated on demand.
 * Stampede protection for CSS and JS aggregation. Uses locking so multiple
   requests for the same thing will result in only one thread doing the work.
 * Zero file I/O if the Aggregated file already exists. Results in better page
   generation performance.
 * Fully cached CSS/JS assets making this module faster than drupal core.
 * Smarter aggregate deletion. CSS/JS aggregates only get removed from the cache
   if they have not been used/accessed in the last 3 days.
 * Smarter cache flushing. Scans all CSS/JS files that have been added to any
   aggregate; if that file has changed then rebuild all aggregates that contain
   the updated file and give the newly aggregated file a new name. The new name
   ensures changes go out when using CDNs.
 * Works with Drupal's private file system. Can Use a separate directory for
   serving aggregated files from.
 * Footer JS gets aggregated as well.
 * One can add JS to any region & have it aggregated.
   drupal_add_js($data, 'module', 'left') is now possible; JS is appended to the
   the end of that region.
 * One can add external JS/CSS resources.
    drupal_add_js('http://example.org/example.js', 'external');
    drupal_add_css('http://example.org/example.css', 'external');
   is now possible.
 * Url query string to turn off aggregation for that request. ?advagg=0 will
   turn off file aggregation if the user has the "bypass advanced aggregation"
   permission. ?advagg=-1 will completely bypass all of Advanced CSS/JS
   Aggregations modules and submodules.
 * Button on the admin page for dropping a cookie that will turn off file
   aggregation. Useful for theme development.
 * Url query string to turn on advagg debugging for that request.
   ?advagg-debug=1 will output a large debug string to the watchdog if the user
   has the "bypass advanced aggregation" permission.
 * Gzip support. All aggregated files can be pre-compressed into a .gz file and
   served from Apache. This is faster then gzipping the file on each request.
 * IE Unlimited CSS support. If using ?advagg=0 the CSS output will change
   to use @import style in order to get around the 31 CSS files limit in IE.
 * CDN support. Advagg integrates with this module.
 * jQuery Update support. Advagg integrates with this module.
 * LABjs support. Advagg integrates with this module.
 * One year browser cache lifetimes for all aggregated files. This is a good
   thing.
 * Drush support. "cc advagg" will issue a smart cache flush.
 * Admin menu support. Cache flushing Advanced CSS/JS Aggregation is available
   in the "Flush all caches" menu.

Advanced CSS/JS Aggregation Submodules:
 CSS:
 * CSSTidy library support. Can compress the generated CSS files with the
   CSSTidy library.
 * CSS Compressor 3.0 support. https://github.com/codenothing/css-compressor
 JS:
 * JSMin+ library support. Can compress the generated JS files with the jsmin+
   library.
 * JSMin PHP Extension support. http://www.ypass.net/software/php_jsmin/
 * Use Dean Edwards Packer on non gzipped files. .gz files will not be packed.
 CDN:
 * Google's CDN network. Load jquery.js & jquery-ui.js from using the Google
   Libraries API. This is a good thing.
 Bundler:
 * Bundler. Will split up an aggregate into sub aggregates for better load
   times throughout your site.

3rd Party modules:
 CSS:
 * Parallel CSS - AdvAgg Plugin. Have url()'s in css files reference different
   CDN domains.


CONFIGURATION
-------------

Settings page is located at:
admin/settings/advagg
 * Enable Advanced Aggregation. You can disable the module here. Same effect as
   placing ?advagg=-1 in the URL.
 * Use AdvAgg in closure. If enabled javascript files in the closure region will
   be aggregated by advagg.
 * Generate CSS/JS files on request (async mode). If advagg doesn't have a route
   back to its self and this is enabled then you will have a broken site. With
   this enabled one can expect much quicker page generation times after a cache
   flush.
 * Gzip CSS/JS files. For every Aggregated file generated, this will create a
   gzip version of that and then serve that out if the browser accepts gzip
   compression.
 * Generate .htaccess files in the advagg_* dirs. If your using the rules
   located at the bottom of this document in your webroots htaccess file then
   you can disable this checkbox.
 * Regenerate flushed bundles in the cache flush request. You can enable if your
   server will not timeout on a request. This will call advagg_rebuild_bundle()
   as a shutdown function for every bundle that has been marked as expired;
   thus rebuilding that bundle in the same request as the flush.
 * Use a different directory for storing advagg files. Only available if your
   using a private file system. Allows you to save the generated aggregated
   files in a different directory. This gets around the private file system
   restrictions. If boost is installed, you can safely use the cache directory.
 * Aggregation Inclusion Mode. Should the page wait for the aggregate to be
   built before including the file, or should it send out the page with
   aggregates not included.
 * Disable page caching if all aggregates are not included on the page.
 * File Checksum Mode. mtime is the file modification time. md5 is a hash of the
   files contents.
 * IP Address to send all asynchronous requests to. If you wish to have one
   server generate all CSS/JS aggregated files then this allows for that to
   happen.
 * Smart cache flush button. Scan all files referenced in aggregated files. If any of
   them have changed, increment the counters containing that file and rebuild
   the bundle.
 * Cache Rebuild button. Recreate all aggregated files. Useful if JS or CSS
   compression was just enabled.
 * Forced Cache Rebuild. Recreate all aggregated files by incrementing internal
   counter for every bundle. One should never have to use this option.
 * Master Reset. Clean slate; same affect as uninstalling the module.
 * Rebuild htaccess files. Recreate the generated htaccess files.
 * Aggregation Bypass Cookie. This will set or remove a cookie that disables
   aggregation for the remainder of the browser session. It acts almost the same
   as adding ?advagg=0 to every URL.

Additional information is available at:
admin/settings/advagg/info
 * Hook Theme Info. Displays the preprocess_page order. Used for debugging.
 * CSS files. Displays how often a files checksum has changed and any data
   stored about it.
 * JS files. Displays how often a files checksum has changed and any data
   stored about it.
 * Modules implementing advagg hooks. Lets you know what modules are using
   advagg.
 * Missing files. Lets you know the files that are trying to be added but are
   not there.
 * Asynchronous debug info. Outputs the the full object returned from
   drupal_http_request() which is helpful when debugging async issues.


TECHNICAL DETAILS & HOOKS
-------------------------

Technical Details:
 * There are two database tables and two cache table used by advagg.
 * Files are generated by this pattern: css_[MD5]_[Counter].css
 * Every JS file is tested for compressibility. This is necessary because jsmin+
   can run out of memory on certain files. This allows us to catch these bad
   files and mark them. Also allows us to skip files that are already
   compressed.

Hooks:
 * hook_advagg_css_alter. Modify the data before it gets written to the file.
   Useful for compression.
 * hook_advagg_css_inline_alter. Modify the data before it gets embedded in the
   page. Useful for compression.
 * hook_advagg_css_pre_alter. Modify the raw $variables['css'] before it gets
   processed. Useful for file replacement.
 * hook_advagg_css_extra_alter. Allows one to set the a prefix and suffix to be
   added into the HTML DOM. Useful for CSS conditionals.

 * hook_advagg_js_alter. Modify the data before it gets written to the file.
   Useful for compression.
 * hook_advagg_js_inline_alter. Modify the data before it gets embedded in the
   page. Useful for compression.
 * hook_advagg_js_pre_alter. Modify the raw $javascript before it gets
   processed. Useful for file replacement.
 * hook_advagg_js_extra_alter. Allows one to set the a prefix and suffix to be
   added into the HTML DOM.
 * hook_advagg_js_header_footer_alter. Allows one to move JS from the header to
   the footer. Also one can look at both header and footer JS arrays before they
   get processed.

 * hook_advagg_filenames_alter. Allows for a one to many relationship. A single
   request for a bundle name can result in multiple bundles being returned.
 * hook_advagg_files_table. Allows for modules to mark a file as expired.
 * advagg_master_reset. Allows other modules to take part in a master reset.
 * advagg_disable_processor. Allows one to turn off advagg from a hook. See the
   advagg_advagg_disable_processor() function for example usage.
 * advagg_disable_page_cache. Allows 3rd party page cache plugins like boost or
   varnish to not cache this page.
 * advagg_bundler_analysis_alter. Give installed modules a chance to alter the
   bundler's analysis array.

JS/CSS Theme Override:

    $conf['advagg_css_render_function'] - advagg_unlimited_css_builder
    $conf['advagg_js_render_function'] - advagg_js_builder

JS/CSS File Save Override:

    $conf['advagg_file_save_function'] - advagg_file_saver

Public Functions:
 * advagg_add_css_inline. Adds the ability to add in inline CSS to the page with
   a prefix and suffix being set as well.

SINGLE HTACCESS RULES
---------------------

If the directory level htaccess rules are interfering with your server, you can
place these rules in the Drupal root htaccess file. Place these rules after
"RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]" but before "</IfModule>"


  # Rules to correctly serve gzip compressed CSS and JS files.
  # Requires both mod_rewrite and mod_headers to be enabled.
  <IfModule mod_headers.c>
    # Serve gzip compressed CSS/JS files if they exist and client accepts gzip.
    RewriteCond %{HTTP:Accept-encoding} gzip
    RewriteCond %{REQUEST_URI} (^/(.+)/advagg_(j|cs)s/(.+)\.(j|cs)s) [NC]
    RewriteCond %{REQUEST_FILENAME}\.gz -s
    RewriteRule ^(.*)\.(j|cs)s$ $1\.$2s\.gz [QSA]

    # Serve correct content types, and prevent mod_deflate double gzip.
    RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1]
    RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1]

    <FilesMatch "\.(j|cs)s\.gz$">
      # Serve correct encoding type.
      Header set Content-Encoding gzip
      # Force proxies to cache gzipped & non-gzipped css/js files separately.
      Header append Vary Accept-Encoding
    </FilesMatch>
  </IfModule>


You also need to place these rules at the very end of your htaccess file, after
"</IfModule>".


<FilesMatch "^(j|cs)s_[0-9a-f]{32}_.+\.(j|cs)s(\.gz)?">
  <IfModule mod_expires.c>
    # Enable expirations.
    ExpiresActive On

    # Cache all aggregated CSS/JS files for 1 year after access (A).
    ExpiresDefault A31556926
  </IfModule>
  <IfModule mod_headers.c>
    # Unset unnecessary headers.
    Header unset Last-Modified
    Header unset Pragma
    Header unset Accept-Ranges

    # Make these files publicly cacheable.
    Header append Cache-Control "public"
  </IfModule>
  FileETag MTime Size
</FilesMatch>


Be sure to disable the "Generate .htaccess files in the advagg_* dirs" setting
on the admin/settings/advagg page after placing these rules in the webroots
htaccess file. This is located at the same directory level as Drupal's
index.php.

NGINX CONFIGURATION
-------------------
http://drupal.org/node/1116618

    ###
    ### advagg_css and advagg_js support
    ###
    location ~* advagg_(?:css|js)/ {
        access_log off;
        expires 365d;
        add_header Pragma "";
        add_header Cache-Control "public";
        try_files $uri @drupal;
    }
