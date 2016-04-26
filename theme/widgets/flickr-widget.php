<?php
class flickr_widget extends WP_Widget {

	public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'flickr_widget',
            // Widget name will appear in UI
            __('SH-Themes Flickr Widget', 'shcreate'),

            // Widget description
            array( 'description' => __( 'Flickr image feed', 'shcreate' ), )
        );
    }

	function widget( $args, $instance ) {
		extract( $args );
		$title 			= apply_filters('widget_title', $instance['title'] );
		$photo_source 	= $instance['photo_source'];
		$flickr_id 		= $instance['flickr_id'];
		$flickr_tag 	= $instance['flickr_tag'];
		$display 		= $instance['display'];
		$photo_number 	= $instance['photo_number'];

		echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			$proto = 'http';
        	if (is_ssl()) {
            	$proto = 'https';
        	}

			echo '
				<script type="text/javascript" src="' . $proto . '://www.flickr.com/badge_code_v2.gne?count='; 
				if ( $photo_number ) {
					printf( '%1$s', esc_attr( $photo_number ) ); echo '&amp;display=';
				}
				if ( $display )  {
					printf( '%1$s', esc_attr( $display ) ); echo '&amp;layout=x&amp;';
				}
				
				if ( $instance['photo_source'] == 'user' ) { 
					printf( 'source=user&amp;user=%1$s', esc_attr( $flickr_id ) );
				}
				elseif ( $instance['photo_source'] == 'group' ) {
					printf( 'source=group&amp;group=%1$s', esc_attr( $flickr_id ) );
				}
				if  ( $instance['photo_source'] == 'all_tag' ) {
					printf( 'source=all_tag&amp;tag=%1$s', esc_attr( $flickr_tag ) ); 
				}

				echo '&amp;size=m';

				echo '"></script>';
				
			echo '<div class="clear clearfix"></div>';
			
		echo $after_widget; 
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['photo_source'] 	= ( ! empty( $new_instance['photo_source'] ) ) ? sanitize_text_field( $new_instance['photo_source'] ) : '';
		$instance['flickr_id'] 		= ( ! empty( $new_instance['flickr_id'] ) ) ? sanitize_text_field( $new_instance['flickr_id'] ) : '';
		$instance['flickr_tag'] 	= ( ! empty( $new_instance['flickr_tag'] ) ) ? sanitize_text_field( $new_instance['flickr_tag'] ) : '';
		$instance['display'] 		= ( ! empty( $new_instance['display'] ) ) ? sanitize_text_field( $new_instance['display'] ) : '';
		$instance['photo_number'] 	= ( ! empty( $new_instance['photo_number'] ) ) ? sanitize_text_field( (int)$new_instance['photo_number'] ) : '';

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => 'Flickr Photo Stream',
			'flickr_id' => '',
			'photo_source' => 'all_tag',
			'display' => 'latest',
			'photo_number' => '6',
			'flickr_tag' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		if (isset($items)) 
			$items  = (int) $items;
		else 
			$items = 0;
			
		if (isset($items) && $items < 1 || 10 < $items )
		$items  = 10;
		?>
		
		<div class="controlpanel">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','shcreate'); ?></label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" type="text" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'photo_source' ); ?>"><?php _e('Image Source','shcreate'); ?></label> 
				<select id="<?php echo $this->get_field_id( 'photo_source' ); ?>" name="<?php echo $this->get_field_name( 'photo_source' ); ?>">
					<option value="user" <?php if ( 'user' == $instance['photo_source'] ) echo 'selected="selected"'; ?>><?php _e('User','shcreate'); ?></option>
					<option value="group" <?php if ( 'group' == $instance['photo_source'] ) echo 'selected="selected"'; ?>><?php _e('Group','shcreate'); ?></option>
					<option value="all_tag" <?php  if ( 'all_tag' == $instance['photo_source'] ) echo 'selected="selected"'; ?>><?php _e('All Users Photos (based on tags)','shcreate'); ?></option>			
				</select>
			</p>
			
			<div rel="flickr_id">
				<p>
					<label for="<?php echo $this->get_field_id( 'flickr_id' ); ?>"><?php _e('User or Group ID','shcreate'); ?> <b>(<?php echo __('check at idgettr.com', 'shcreate');?>)</b></label>
					<input id="<?php echo $this->get_field_id( 'flickr_id' ); ?>" name="<?php echo $this->get_field_name( 'flickr_id' ); ?>" value="<?php echo esc_attr( $instance['flickr_id'] ); ?>" class="widefat" type="text" />
				</p>
			</div>
			
			<div rel="flickr_tag">
				<p>
					<label for="<?php echo $this->get_field_id( 'flickr_tag' ); ?>"><?php _e('Tags (separate with comma) (only if "All Users Photos" selected)','shcreate'); ?></label>
					<input id="<?php echo $this->get_field_id( 'flickr_tag' ); ?>" name="<?php echo $this->get_field_name( 'flickr_tag' ); ?>" value="<?php echo esc_attr( $instance['flickr_tag'] ); ?>" class="widefat" type="text" />
				</p>
			</div>

			<p>
				<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e('Display Latest or Random Photos','shcreate'); ?></label> 
				<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
					<option value="latest" <?php selected( $instance['display'], 'latest' ); ?>><?php _e('Latest','shcreate'); ?></option>
					<option value="random" <?php selected( $instance['display'], 'random' ); ?>><?php _e('Random','shcreate'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_name( 'photo_number' ); ?>"><?php _e('How many items would you like to display?','shcreate'); ?></label>
				<select id="<?php echo $this->get_field_id( 'photo_number' ); ?>" name="<?php echo $this->get_field_name( 'photo_number' ); ?>">			
				<?php
					for ( $i = 1; $i <= 10; ++$i )
					echo "<option value='$i' " . selected( $instance['photo_number'], $i, false ) . ">$i</option>";
				?>
				</select>
			</p>
			
		</div>
		
	<?php
	}
}

function register_flickr() {
	register_widget('flickr_widget');
}

add_action('widgets_init', 'register_flickr');
