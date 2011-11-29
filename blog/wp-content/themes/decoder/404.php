<?php get_header(); ?>

<div id="content" class="clearfix">

	<div id="content-left" id="searchform">

		<h3 class="post-title">Sorry, not found!</h3>
		
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	
	</div><!-- end content-left -->
	  
	  <?php get_sidebar(); ?>
	
</div><!-- end content -->
	
<?php get_footer(); ?>