<?php
include("../config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title>
		<?php if ( is_home() ) { ?><?php bloginfo('description'); ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_search() ) { ?><?php echo $s; ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_single() ) { ?><?php wp_title(''); ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_page() ) { ?><?php wp_title(''); ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_category() ) { ?>Archive <?php single_cat_title(); ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_month() ) { ?>Archive <?php the_time('F'); ?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_tag() ) { ?><?php single_tag_title();?> &raquo; <? bloginfo('name'); ?><?php } ?>
		<?php if ( is_404() ) { ?>Sorry, not found! &raquo; <? bloginfo('name'); ?><?php } ?>
</title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/lib/superfish.css" media="screen">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/lib/js/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/lib/js/superfish.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/lib/js/supersubs.js"></script>

<!-- Start FaceBox Include -->
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/lib/js/facebox/facebox.css" type="text/css" media="all" />
<script src="<?php bloginfo('template_directory'); ?>/lib/js/facebox/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
	   $('a[rel*=facebox]').facebox() 
	}) 
</script>
<!-- End FaceBox Include -->

<script type="text/javascript"> 
 
    $(document).ready(function(){ 
        $("ul.sf-menu").supersubs({ 
            minWidth:    12,   // minimum width of sub-menus in em units 
            maxWidth:    27,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
        }).superfish();
    });
 
</script>

<?php if (is_singular()) wp_enqueue_script( 'comment-reply' ); wp_head(); ?>

</head>

<body>

<div id="page">

	<div id="headerWrapper">
		<div id="header">

			<div id="blog-logo" class="clearfix">
				<a href="<?= $baseurl; ?>"><img src="<?php bloginfo('template_directory'); ?>/img/banner-thin-glass.png" alt="<?php bloginfo( 'name' ) ?>" /></a>
			</div>
			
			<div id="nav" style="width: 100%;">
				<div style="width: 960px; margin: 0px auto;">
					<ul>
						<li id="nav_donation" class="tab"><a href="<?= $baseurl ?>/?tab=donation"></a></li>
						<li id="nav_forum" class="tab"><a target="_blank" href="http://forums.thegamesdb.net"></a></li>
						<li id="nav_stats" class="tab"><a href="<?= $baseurl ?>/?tab=stats"></a></li>
					<?php if ($loggedin): ?>
							<li id="nav_submit" class="tab"><a href="<?= $baseurl ?>/?tab=addgame"></a></li>
					<?php endif; ?>
					</ul>
					<form id="search" action="<?= $baseurl ?>/search/">
						<input class="autosearch" type="text" name="string" style="color: #333; margin-left: 40px; margin-top: 5px; width: 190px;" />
						<input type="hidden" name="function" value="Search" />
						<input type="submit" value="Search" style="margin-top: 4px; margin-left: 4px; height: 24px;" />
					</form>
				</div>
			</div>
			
			<div id="rss">
				<a href="<?php bloginfo('rss2_url'); ?>">Subscribe to RSS Feed</a>
			</div>

			<ul id="menu" class="sf-menu clearfix">
				<li><span style="color: orange; font-weight: bold; line-height: 35px;">Blog Navigation ></span></li>
				<li class="cat_item<?php if(is_home()) echo ' current-cat'; ?>"><a href="<?php bloginfo('url'); ?>">Home</a></li>
				<?php wp_list_categories('title_li=&sort_column=menu_order'); ?>
			</ul>

		</div>
		<div style="background: url(<?php bloginfo('template_directory'); ?>/img/bg_banner-shadow.png) repeat-x center center; height: 15px; width: 100%; z-index: 299; opacity: 0.5;"></div>
	</div><!-- end header -->
	
	<div id="contentWrapper" class="clearfix">