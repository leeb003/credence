<?php
// Creating the widget 
class contact_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			// Base ID of your widget
			'contact_widget', 
			// Widget name will appear in UI
			__('Contact Info Widget', 'shcreate'), 

			// Widget description
			array( 'description' => __( 'Contact fields from Theme Options (also used in contact form)', 'shcreate' ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		global $shcreate;
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is where you run the code and display the output
		?>
		<div class="contact-info">
    		<?php
				if (isset($shcreate['opt-multi-contacts'][0])) {
    				foreach ($shcreate['opt-multi-contacts'] as $k => $v) {
    		?>
    		<p><i class="<?php echo $shcreate['opt-multi-contacts-fa'][$k];?>"></i>
    			<?php echo html_entity_decode($v);?>
    		</p>
    			<?php } ?>
			<?php } ?>
		</div>
		<?php
		echo $args['after_widget'];

	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'shcreate' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'shcreate' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // End Class

// Register and load the widget
function theme_load_widgets() {
	register_widget( 'contact_widget' );
}
add_action( 'widgets_init', 'theme_load_widgets' );

