<?php get_header(); ?>

	<div id="content">
	
		<div id="content-left">

			<h2>Archives by Month:</h2>
				
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>

			<h2>Archives by Subject:</h2>
			
				<ul>
					<?php wp_list_categories('title_li='); ?>
				</ul>
	
	  </div><!-- end content-left -->
	  
	  <?php get_sidebar(); ?>
	  
	  <div class="clear"></div>
	
</div><!-- end content -->
	
<?php get_footer(); ?>