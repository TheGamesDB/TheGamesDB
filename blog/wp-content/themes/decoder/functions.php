<?php

// Widget Settings

if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name' => 'Sidebar',
		'before_widget' => '<div id="%1$s" class="box-right">', 
	'after_widget' => '</div>', 
	'before_title' => '<h4 class="sidebar-title">', 
	'after_title' => '</h4>', 
	));
	
function widget_webdemar_search() {
?>
    	<div class="box-right">
		<h4 class="sidebar-title">Search</h4>
			<div id="searchform">
				<form method="get" action="<?php bloginfo('url'); ?>/">
					<input type="text" name="s" id="search" />&nbsp;
					<input type="submit" id="search-submit" class="button" name="submit" value="Search" />
				</form>
			</div>
		</div>
	
<?php
}

if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('deCoder Search'), 'widget_webdemar_search');
    
    
// If not WP 2.7 call comments.old.php
    
add_filter('comments_template', 'legacy_comments');

function legacy_comments($file) {

	if(!function_exists('wp_list_comments')) : // WP 2.7-only check
		$file = TEMPLATEPATH . '/comments.old.php';
	endif;

	return $file;
}


// add a microid to all the comments

function comment_add_microid($classes) {
	$c_email=get_comment_author_email();
	$c_url=get_comment_author_url();
	if (!empty($c_email) && !empty($c_url)) {
		$microid = 'microid-mailto+http:sha1:' . sha1(sha1('mailto:'.$c_email).sha1($c_url));
		$classes[] = $microid;
	}
	return $classes;	
}
add_filter('comment_class','comment_add_microid');

?>