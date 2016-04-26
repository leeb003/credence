<?php
// Creating the widget 
class like_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			// Base ID of your widget
			'like_widget', 
			// Widget name will appear in UI
			__('SH-Themes Like Ranking Widget', 'shcreate'), 

			// Widget description
			array( 'description' => __( 'Like rankings of posts', 'shcreate' ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$lcount = apply_filters( 'widget_count', $instance['lcount'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is where you run the code and display the output
		?>
		<ul class="like-rank">
    		<?php
			    global $wpdb;
				//$sql = "select * from " . $wpdb->prefix . "postmeta where meta_key='_post_like_count'"
                //     . " order by meta_value desc limit " . $count;
				// can't sort longtext fields numerically and be acurate, so we need to get all and then sort results.
				$sql = "select * from " . $wpdb->prefix . "postmeta where meta_key='_post_like_count'";
				$results = $wpdb->get_results($sql);

				// PHP version check older usort function call used to work with all 5.2+ versions, commented out newer method
				// as errors prevent it from working on old php installs
				//if (version_compare(phpversion(), '5.3.10', '<')) {
					// php version isn't high enough
					usort($results, array($this, "old_like_sort"));
				//} else {
				//	usort($results, function($a, $b) {
						//return $a->meta_value - $b->meta_value; // ascending
				//		return $b->meta_value - $a->meta_value; // descending
				//	});
				//}

				$inc = 0;
				foreach($results as $k => $v) {
					$inc++;
					if ($inc <= $lcount) {
						$permalink = get_permalink($v->post_id);
						$postTitle = get_the_title($v->post_id);
						$count = $v->meta_value;
						$theDate = get_the_time(get_option('date_format', $v->post_id));
    			?>
    		<li>
				<div class="like-left">
					<i class="fa fa-plus-square behind"></i><br />
					<?php echo '<i class="fa fa-plus-square"></i> ' . $count; ?> 
				</div>
				<div class="like-post">
					<a href="<?php echo $permalink;?>"><?php echo $postTitle; ?></a>
					<br /> <span class="like-date"><?php echo $theDate;?></span>
				</div>
			</li>
					<?php } ?>
    			<?php } ?>
		</ul>
		<?php
		echo $args['after_widget'];
	}

	// Old usort function (pre php 5.3.10)
	public function old_like_sort($a, $b) {
		return $b->meta_value - $a->meta_value;
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Like Rank', 'shcreate' );
		}
		if ( isset($instance[ 'lcount' ] ) ) {
			$count = esc_attr($instance[ 'lcount' ]);
		} else {
			$count = '';
		}

		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'shcreate' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="lcount"><?php _e( 'Result Count:', 'shcreate' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'lcount' ) ?>" name="<?php echo $this->get_field_name( 'lcount' ) ?>" style="width:100%;"> 
		<?php 
			for ($i=1; $i < 11; $i++) {
		?>
			<option <?php selected($count, $i); ?> value="<?php echo $i;?>"> <?php echo $i;?> results</option>
		<?php } ?>
		</select>

		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['lcount'] = ( ! empty( $new_instance['lcount'] ) ) ? strip_tags( $new_instance['lcount'] ) : '';
		return $instance;
	}
} // End Class

// Register and load the widget
function theme_load_likes() {
	register_widget( 'like_widget' );
}
add_action( 'widgets_init', 'theme_load_likes' );

