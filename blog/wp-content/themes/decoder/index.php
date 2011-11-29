<?php get_header(); ?>

<div id="content" class="clearfix">

	<div id="content-left">

	<h2 style="margin: 10px 0px; text-align: center;">Welcome to TheGamesDB.net!</h2>
	<p style="text-align: center;">This website aims to be the top resource for video game scraping via our API. We strive to have the highest quality available in our artwork and metadata. This site is open and entirely community driven, and relies on user submissions for content. We host Fanart, Banners, Covers, and Metadata that can be incorporated into media center front-ends for HTPC&#39;s in various ways. Please feel free to contribute!</p>
	<p style="text-align: center;">For updates and site news, keep an eye on the posts below.</p>
	<hr style="background-color: #aaa; margin: 25px 0px; height: 1px" />
	
<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
	
			<div id="post-<?php the_ID(); ?>" <?php if(function_exists('post_class')) : post_class(); else : echo 'class="post"'; endif; ?>>
			
				<h3 class="post-title">
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h3>
				
				<?php the_content(__('&rsaquo; Continue reading')); ?>
				
				<?php the_tags('<p class="tags">Tags: ', ', ', '</p>'); ?>
				
				<?php edit_post_link('[edit post]', '<p>', '</p>'); ?>
				
				<div class="meta">
					<span class="meta-date"><?php the_time('l, F jS, Y'); ?></span>
					<span class="meta-categories"><?php the_category(', '); ?></span>
					<span class="meta-comments"><?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments')); ?></span>
				</div>
				
				<div class="clear"></div>
				
			</div>
		
		<?php endwhile; ?>
		
		<div class="box-left navigation">
		
        	<?php next_posts_link('&laquo; Previous Entries') ?> <?php previous_posts_link('Next Entries &raquo;') ?>
        	
		</div>
		
		<?php else : ?>
		
		<div class="box-left" id="searchform">

			<h3>Not found!</h3>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		
		</div>

<?php endif; ?>
	
	  </div><!-- end content-left -->
	  
	  <?php get_sidebar(); ?>
	  
	  <div class="clear"></div>
	
</div><!-- end content -->
	
<?php get_footer(); ?>