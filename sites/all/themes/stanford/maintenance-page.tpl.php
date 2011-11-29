<?php
/**
 * @file maintenance-page.tpl.php
 *
 * Theme implementation to display a single Drupal page while off-line.
 *
 * All the available variables are mirrored in page.tpl.php. Some may be left
 * blank but they are provided for consistency.
 *
 *
 * @see template_preprocess()
 * @see template_preprocess_maintenance_page()
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language ?>" xml:lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
<head>
<title><?php print $head_title ?></title>
<?php print $head ?><?php print $styles ?><?php print $scripts ?>
</head>
<body class="<?php print $body_classes; ?>">
<!-- Start #layout -->
<div id="layout"> 
  <!-- Start #wrapper -->
  <div id="wrapper"> 
    <!-- Start #header -->
    <div id="header"> <?php print $header; ?> <?php print '';
            if ($logo) {
              print '<div id="logo"><a href="http://www.stanford.edu"><img src="'. check_url($logo) .'" alt="Stanford University" /></a></div>';
            }
			print '<div id="site">';
			if ($site_name) {
              print '<div id="name"><a href="'. check_url($front_page) .'" title="'. check_plain($site_name) .'">'. check_plain($site_name) .'</a></div>';
            }
            if ($site_slogan) {
              print '<div id="slogan">'. check_plain($site_slogan) .'</div>';
            }
            print '</div><br class="clear" />';
          ?> </div>
    <!-- End #header --> 
    
    <!-- Start #container -->
    <div id="container">
      <?php if ($top): ?>
      <div id="top"><?php print $top ?></div>
      <?php endif; ?>
      <?php if ($title): print '<h1'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h1>'; endif; ?>
      <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
      <div id="content">
        <?php if ($left): ?>
        <div id="sidebar-left" class="sidebar"> <?php print $left ?> </div>
        <!-- /#sidebar-left -->
        <?php endif; ?>
        <div id="center">
          <?php if ($upper): ?>
          <div id="upper"><?php print $upper ?></div>
          <?php endif; ?>
          <?php print $breadcrumb; ?>
          <?php if ($middle): ?>
          <div id="middle"><?php print $middle ?></div>
          <?php endif; ?>
          <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
          <?php if ($tabs): print '<ul class="tabs primary">'. $tabs .'</ul></div>'; endif; ?>
          <?php if ($tabs2): print '<br /><ul class="tabs secondary">'. $tabs2 .'</ul>'; endif; ?>
          <?php if ($show_messages && $messages): print $messages; endif; ?>
          <?php print $help; ?> <?php print $content ?>
          <?php if ($lower): ?>
          <div id="lower"><?php print $lower ?></div>
          <?php endif; ?>
        </div>
        <!-- /#center -->
        <?php if ($right): ?>
        <div id="sidebar-right" class="sidebar"> <?php print $right ?> </div>
        <!-- /#sidebar-right -->
        <?php endif; ?>
        <div class="content_clear"></div>
      </div>
      <!-- /#content -->
      <div id="footer-wrapper">
        <?php if ($bottom): ?>
        <div id="bottom"><?php print $bottom ?></div>
        <?php endif; ?>
        <div id="footer"><?php print $footer ?><?php print $feed_icons ?></div>
        <!-- /#footer -->
        <div id="copyright" class="vcard"><?php print $footer_message ?></div>
        <!-- /#copyright --> 
      </div>
      <!-- /#footer-wrapper --> 
    </div>
    <!-- /#container --> 
  </div>
  <!-- /#wrapper --> 
</div>
<!-- /layout --> 

<?php print $closure ?>
</body>
</html>
