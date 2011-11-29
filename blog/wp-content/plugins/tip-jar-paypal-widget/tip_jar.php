<?php
/*

Plugin Name: Tip Jar
Plugin URI: http://ryan.sadwick.com/tip_jar/
Description: Tip jar widget that uses Paypal buttons.
Version: 1.1
Author: Ryan Sadwick
Author URI: http://ryan.sadwick.com

*/


class Tip_Jar extends WP_Widget {

	function Tip_Jar() {
		$widget_ops = array('classname' => 'widget_tip_jar', 'description' => 'A Paypal donation button widget.' );
		$this->WP_Widget('tip_jar', 'Tip Jar', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$tip_desc = empty($instance['tip_desc']) ? ' ' : apply_filters('widget_tip_desc', $instance['tip_desc']);
		$pp_id = apply_filters( 'widget_text', $instance['pp_id'], $instance );
		
		echo $before_widget;
		//Check to see if title and description are set:
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		if ( !empty( $tip_desc ) ) { echo '<p class="widget_tip_desc">' . $tip_desc . '</p>'; };
		if ( !empty( $pp_id ) ) { echo '<p>' . $pp_id . '</p>'; };

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['tip_desc'] = strip_tags($new_instance['tip_desc']);
		
		if ( current_user_can('unfiltered_html') )
			$instance['pp_id'] =  $new_instance['pp_id'];
		else
			$instance['pp_id'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['pp_id']) ) ); // wp_filter_post_kses() expects slashed

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'pp_id' => '', 'btn_url' => '', 'tip_desc' => '' ) );
		$title = strip_tags($instance['title']);
		$pp_id = esc_textarea($instance['pp_id']);
		$tip_desc = strip_tags($instance['tip_desc']);
?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('tip_desc'); ?>">Description: <input class="widefat" id="<?php echo $this->get_field_id('tip_desc'); ?>" name="<?php echo $this->get_field_name('tip_desc'); ?>" type="text" value="<?php echo attribute_escape($tip_desc); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('pp_id'); ?>">Paypal code:<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('pp_id'); ?>" name="<?php echo $this->get_field_name('pp_id'); ?>"><?php echo $pp_id; ?></textarea></label></p>
			
<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("Tip_Jar");') );
?>