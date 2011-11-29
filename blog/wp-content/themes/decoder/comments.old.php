<?php // Do not delete these lines
	if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments"><?php _e('Enter your password to view comments.'); ?></p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'alt';
?>

<!-- You can start editing here. -->

<?php if ($comments) : ?>

<div id="comments">

	<h4 class="content-title"><?php comments_number(__('No Comments'), __('1 User Comment'), __('% User Comments')); ?></h4>

<?php foreach ($comments as $comment) : $cn++; ?>

	<?php $comment_type = get_comment_type(); ?>
	<?php if($comment_type == 'comment') { ?>

		<div class="<?php if($cn==1) echo 'first '; ?>comment clearfix <?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
						
					<div class="comment-left">
						
						<?php echo get_avatar( $comment, 80, $avatar); ?>
						<div class="comment-author"><?php comment_author_link() ?></div>
						<?php comment_date('F jS, Y') ?>
						
					</div>
						
					<div class="comment-right">
						
						<div class="comment-text">
							<?php if ($comment->comment_approved == '0') : ?>
      							<p><em>Your comment is awaiting moderation.</em></p>
  							<?php endif; ?> 
  							<?php comment_text() ?>
						</div>
						<?php edit_comment_link('[edit comment]','<p style="text-align:right">','</p>'); ?>
						
					</div>
				
				</div><!-- end comment -->
				
<?php
/* Changes every other comment to a different class */
$oddcomment = ( empty( $oddcomment ) ) ? 'alt' : '';
?>

	<?php } else { $trackback = true; } // endif is_comment ?>

<?php endforeach; // end foreach comment ?>
	
	<?php if ($trackback == true) { ?>
	
	<div id="trackbacks">

	<h4 class="content-title">Trackbacks</h4>
	
		<ul>

			<?php foreach ($comments as $comment) : ?>

				<?php $comment_type = get_comment_type(); ?>
				<?php if($comment_type != 'comment') { ?>

					<li id="comment-<?php comment_ID(); ?>">
            			<?php comment_author_link() ?> <?php comment_excerpt() ?>
        			</li>

				<?php } // if type comment_type trackback ?>

			<?php endforeach; // end foreach trackback ?>
	
		</ul>
		
	</div><!-- end trackbacks -->
	
	<?php } // endif trackbacks ?>

</div><!-- end comments -->

<?php else : // this is displayed if there are no comments so far ?>

<div id="comments">

	<?php if ('open' == $post->comment_status) : ?>
		
		<!-- No comments yet -->
		
	 <?php endif; ?>
	 
</div><!-- end comments -->

<?php endif; // endif comments ?>

<?php if ('open' == $post->comment_status) : ?>

<div id="response" class="clearfix">

	<h4 class="content-title"><?php _e('Leave a comment'); ?></h4>
	
	<fieldset>

		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p class="alert"><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), get_option('siteurl')."/wp-login.php?redirect_to=".urlencode(get_permalink()));?></p>
		<?php else : ?>

		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
			
				<div id="commentform-left">
						
					<img src="<?php bloginfo('template_url'); ?>/img/avatar-commentform.gif" alt="you" width="80" height="80"/><br />
					<div class="comment-author">Your Name</div>
					<?php echo date('F jS, Y'); ?>
						
				</div>
				
				<div id="commentform-right">
				
					<textarea name="comment" id="comment-textarea" tabindex="1"></textarea>
				
					<?php if ( $user_ID ) : ?>

					<p style="float:left;margin:20px 0 0 0"><?php printf(__('Logged in as %s.'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> (<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Logout'); ?></a>)</p>

					<?php else : ?>
					
					<label for="username">Username</label>
					<input type="text" name="username" id="username" value="<?php echo $comment_author; ?>" class="text" tabindex="2" />
					<label for="email">Email (will not be published)</label>
					<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" class="text" tabindex="3" />
					<label for="url">URL (optional)</label>
					<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" class="text" tabindex="4" />

					<?php endif; ?>

					<input name="submit" type="submit" class="submit" tabindex="5" value="<?php echo attribute_escape(__('Submit Comment')); ?>" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					
				</div><!-- end commentform-right -->

			<?php do_action('comment_form', $post->ID); ?>

		</form>
		
		<?php endif; // If registration required and not logged in ?>

	</fieldset>

</div><!-- end commentform -->

<?php endif; // if you delete this the sky will fall on your head ?>