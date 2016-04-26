<?php
class portfolio_widget extends WP_Widget {

	public function __construct() {
        parent::__construct(
            // Base ID of your widget
            'portfolio_widget',
            // Widget name will appear in UI
            __('SH-Themes Portfolio Widget', 'shcreate'),

            // Widget description
            array( 'description' => __( 'Displays your portfolio items', 'shcreate' ), )
        );
    }

	/* 
	   Display the widget
	*/
	function widget( $args, $instance ) {
		extract( $args );
		$title 			= apply_filters('widget_title', $instance['title'] );
		$categories 	= $instance['categories'];
		$photo_number 	= $instance['photo_number'];
		$portfolio = '';

		echo $before_widget;

 		// Query the post, get portfolio items
		if (trim($categories) != '') {  // query one or multiple portcats
            $cats = explode(',', $categories);
			foreach ($cats as $cat) {
        		$args = array(
            		'post_type' => 'portfolio_entry',
            		'portcat' => $cat,
            		'posts_per_page' => $photo_number
        		);
				$my_query = new WP_Query( $args );
        		while ( $my_query->have_posts() ) : $my_query->the_post();

            		$thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'thumbnail img-responsive'));
            		$permalink = get_the_permalink();
            		$port_title = get_the_title();

            		/* Build the output, and Insert category name into portfolio-item class */
            		$portfolio .= '<div class="col-md-3 col-sm-3 col-xs-3 port-widget-single">'
                        . '<a class="port-widget-link" href="' . $permalink . '" title="' . $port_title . '">' . $thumbnail .'</a>'
                        . '</div>';
        		endwhile;
        		wp_reset_query();
			}
        } else {  // all portfolio categories
			$args = array(
                    'post_type' => 'portfolio_entry',
                    'posts_per_page' => $photo_number
                );

			$my_query = new WP_Query( $args );
			while ( $my_query->have_posts() ) : $my_query->the_post();

    		$thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'thumbnail img-responsive'));
    		$permalink = get_the_permalink();
    		$port_title = get_the_title();

    		/* Build the output, and Insert category name into portfolio-item class */
    		$portfolio .= '<div class="col-md-3 col-sm-3 col-xs-3 port-widget-single">'
                        . '<a class="port-widget-link" href="' . $permalink . '" title="' . $port_title . '">' . $thumbnail .'</a>'
                		. '</div>';
			endwhile;
			wp_reset_query();
		}

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="row portfolio-widget no-padding">'

			. $portfolio

			. '</div>';
				
		echo $after_widget; 
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['categories'] 	= ( ! empty( $new_instance['categories'] ) ) ? sanitize_text_field( $new_instance['categories'] ) : '';
		$instance['photo_number'] 	= ( ! empty( $new_instance['photo_number'] ) ) ? sanitize_text_field( (int)$new_instance['photo_number'] ) : '';

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __('Portfolio', 'shcreate'),
			'photo_number' => '8',
			'categories' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		if (isset($items)) 
			$items = (int) $items;
		else 
			$items = 0;
			
		if (isset($items) && $items < 1 || 16 < $items )
		$items = 16;
		?>
		
		<div class="controlpanel">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','shcreate'); ?></label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
			</p>
			
			<div rel="categories">
				<p>
					<label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e('Categories - (separate with comma, blank shows all)','shcreate'); ?></label>
					<input id="<?php echo $this->get_field_id( 'categories' ); ?>" name="<?php echo $this->get_field_name( 'categories' ); ?>" value="<?php echo esc_attr( $instance['categories'] ); ?>" class="widefat" />
				</p>
			</div>

			<p>
				<label for="<?php echo $this->get_field_name( 'photo_number' ); ?>"><?php _e('How many items would you like to display?','shcreate'); ?></label>
				<select id="<?php echo $this->get_field_id( 'photo_number' ); ?>" name="<?php echo $this->get_field_name( 'photo_number' ); ?>">			
				<?php
					for ( $i = 1; $i <= 16; ++$i )
					echo "<option value='$i' " . selected( $instance['photo_number'], $i, false ) . ">$i</option>";
				?>
				</select>
			</p>
			
		</div>
		
	<?php
	}
}

function register_portfolio() {
	register_widget('portfolio_widget');
}

add_action('widgets_init', 'register_portfolio');
