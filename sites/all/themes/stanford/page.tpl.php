<?php
$nav_classes = theme_get_setting('nav_classes'); 
$layout_classes = theme_get_setting('layout_classes'); 
$header_classes = theme_get_setting('header_classes');
$icon_classes = theme_get_setting('icon_classes'); 
$banner_classes = theme_get_setting('banner_classes'); 
$banner_image_path = theme_get_setting('banner_image_path'); 
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $language->language ?>" xml:lang="<?php print $language->language ?>" dir="<?php print $language->dir ?>">
<head>
<title><?php print $head_title ?></title>
<?php print $head ?><?php print $styles ?><?php print $scripts ?>
</head>
<body class="<?php print $body_classes; ?> <?php if ($primary_links): ?>nav <?php print $nav_classes; ?> <?php if ($nav): ?>drawer<?php endif; ?><?php endif; ?> <?php print $header_classes; ?> <?php if ($is_front): ?><?php print $banner_classes; ?><?php endif; ?> <?php print $layout_classes; ?> <?php print $icon_classes; ?>">
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
    
    <?php if ($primary_links): ?>
    <!-- Start #nav -->
    <div id="nav"><?php print theme('links', $primary_links, array('class' => 'links primary-links')) ?></div>
    <!-- End #nav -->
    
    <?php if ($nav): ?>
    <!-- Start #nav_drawer -->
    <div id="nav_drawer"><?php print $nav ?>
      <div class="clear"></div>
    </div>
    <!-- End #nav_drawer --> 
    
    <!-- Start #toggle -->
    <div id="toggle">
      <div id="menu_expand">Expand Menus</div>
      <div id="menu_hide">Hide Menus</div>
    </div>
    <!-- End #toggle -->
    <?php endif; ?>
    <?php endif; ?>
    
    <!-- Start #container -->
    <div id="container">
      <?php if ($top): ?>
      <div id="top"><?php print $top ?></div>
      <?php endif; ?>
      <?php if ($title): print '<h1'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h1>'; endif; ?>
      <?php if ($mission): print '<div id="mission">'. $mission .'</div>'; endif; ?>
      <div id="content">
        <?php if (($is_front) && ($banner_classes)): ?>
        <div id="banner"><img src="<?php print check_url($front_page) . $banner_image_path; ?>" class="image_banner" /></div>
        <?php endif; ?>
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
        <div id="copyright"><?php print $footer_message ?></div>
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
