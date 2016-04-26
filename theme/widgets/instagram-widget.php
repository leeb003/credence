<?php
/**
 * Description: Instagram Widget, shows up to 20 latest images from a public instagram user.
**/

/**
 * On widgets Init register Widget
 */
add_action( 'widgets_init', array( 'SH_Instagram', 'register_widget' ) );

/**
 * SH Themes Instagram Class
 */
class SH_Instagram extends WP_Widget {
	const VERSION = '1.0';	
	/**
	 * Initialize the plugin by registering widget and loading public scripts
	 *
	 */
	public function __construct() {
		
		// Widget ID and Class Setup
		parent::__construct( 'sh_instagram', __( 'SH-Themes Instagram Widget', 'shcreate' ), array(
				'classname' => 'sh-instawidge',
				'description' => __( 'A widget that shows the latest images from Instagram ', 'shcreate' ) 
			) 
		);

		// Instgram Action to display images
		add_action( 'sh_instagram', array( $this, 'instagram_images' ) );

		// Action when attachments are deleted
		add_action( 'delete_attachment', array( $this, 'delete_wp_attachment' ) );
		
		// Ajax action to unblock images from widget 
		add_action( 'wp_ajax_jr_unblock_images', array( $this, 'unblock_images' ) );

		// Add new attachment field desctiptions
		add_filter( 'attachment_fields_to_edit', array( $this, 'insta_attachment_fields' ) , 10, 2 );
	}

	/**
	 * Register widget on windgets init
	 */
	public static function register_widget() {
		register_widget( __CLASS__ );
	}
	
	/**
	 * The Public view of the Widget  
	 *
	 * @return mixed
	 */
	public function widget( $args, $instance ) {
		
		extract( $args );
		
		//Our variables from the widget settings.
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		
		// Display the widget title 
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		do_action( 'sh_instagram', $instance );
		
		echo $after_widget;
	}
	
	/**
	 * Update the widget settings 
	 *
	 * @param    array    $new_instance    New instance values
	 * @param    array    $old_instance    Old instance values	 
	 *
	 * @return array
	 */
	public function update( $new_instance, $instance ) {
				
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['username']         = $new_instance['username'];
		$instance['source']           = $new_instance['source'];
		$instance['attachment']       = $new_instance['attachment'];
		$instance['images_link']      = $new_instance['images_link'];
		$instance['orderby']          = $new_instance['orderby'];
		$instance['images_number']    = $new_instance['images_number'];
		$instance['columns']          = $new_instance['columns'];
		$instance['refresh_hour']     = $new_instance['refresh_hour'];
		$instance['image_size']       = $new_instance['image_size'];
		$instance['image_link_rel']   = $new_instance['image_link_rel'];
		$instance['image_link_class'] = $new_instance['image_link_class'];
		
		return $instance;
	}
	
	
	/**
	 * Widget Settings Form
	 *
	 * @return mixed
	 */
	public function form( $instance ) {

		$defaults = array(
			'title'            => __('Instagram', 'shcreate'),
			'username'         => '',
			'source'           => 'instagram',
			'attachment' 	   => 1,
			'template'         => 'slider',
			'images_link'      => 'image_url',
			'orderby'          => 'rand',
			'images_number'    => 5,
			'columns'          => 4,
			'refresh_hour'     => 5,
			'image_size'       => 'full',
			'image_link_rel'   => '',
			'image_link_class' => '',
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
			
		?>
		<div class="instagram-container">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'shcreate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" type="text" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Instagram Username:', 'shcreate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" type="text" />
			</p>
			<p>
				<?php _e( 'Source:', 'shcreate' ); ?><br>
				<label class="jr-radio"><input type="radio" class="instagram-source" id="<?php echo $this->get_field_id( 'source' ); ?>" name="<?php echo $this->get_field_name( 'source' ); ?>" value="instagram" <?php checked( 'instagram', $instance['source'] ); ?> /> <?php _e( 'Instagram', 'shcreate' ); ?></label>  
				<label class="jr-radio"><input type="radio" class="instagram-source" id="<?php echo $this->get_field_id( 'source' ); ?>" name="<?php echo $this->get_field_name( 'source' ); ?>" value="media_library" <?php checked( 'media_library', $instance['source'] ); ?> /> <?php _e( 'WP Media Library', 'shcreate' ); ?></label>
				<br><span class="jr-description"><?php _e( 'WP Media Library option will display previously saved instagram images for the user in the field above!', 'shcreate') ?></span>
			</p>
	        <p class="<?php if ( 'instagram' != $instance['source'] ) echo 'hidden'; ?>">
	            <label for="<?php echo $this->get_field_id( 'attachment' ); ?>"><?php _e( 'Copy images to Media Library:', 'shcreate' ); ?></label>
	            <input class="widefat instagram-attach" id="<?php echo $this->get_field_id( 'attachment' ); ?>" name="<?php echo $this->get_field_name( 'attachment' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['attachment'] ); ?> />
	        	<br><span class="jr-description"><?php _e( 'A good idea to improve loading time (and reduce requests to Instagram)', 'shcreate') ?></span>
	        </p>
			<?php 		
				$user_opt = get_option( 'sh_insta_'. md5( $instance['username'] ) );
				if ( isset( $user_opt['deleted_images'] ) && ( !empty( $user_opt['deleted_images'] ) && ( $instance['source'] == 'instagram' ) && ( $instance['attachment'] ) ) ) {
					$deleted_count = count( $user_opt['deleted_images'] );
					echo '<div class="blocked-wrap">';
					wp_nonce_field( 'jr_unblock_instagram_image', 'unblock_images_nonce' );
					echo "<strong>{$instance['username']}</strong> has <strong class='blocked-count-nr'>{$deleted_count}</strong> blocked images! ";
					echo "<a href='#' class='blocked-images-toggle'>[ + Open ]</a>";
					echo '<div class="blocked-images hidden">';
						echo '<ul>';
							foreach ( $user_opt['deleted_images'] as $id => $image ) {
								echo "<li class='blocked-column' data-id='{$id}'><span class='blocked-imgcontainer'><span class='jr-allow-yes dashicons dashicons-yes'></span><img src='{$image}'></span></li>";
							}
						echo '</ul>';
					echo '</div>';
					echo "<span class='jr-description'>You can unblock instagram images by clicking the ones you want to have on the media library.</span>";
					echo '</div>';
				} 
			?>
				<?php
				$image_sizes = array( 'thumbnail', 'medium', 'large' );
				/* 
				$image_size_options = get_intermediate_image_sizes();
				if ( is_array( $image_size_options ) && !empty( $image_size_options ) && !$instance['attachment'] ) {
					$image_sizes = $image_size_options;
				}
				*/
				?>			
				<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image size', 'shcreate' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
					<option value=""><?php _e('Select Image Size', 'shcreate') ?></option>
					<?php
					foreach ( $image_sizes as $image_size_option ) {
						printf( 
							'<option value="%1$s" %2$s>%3$s</option>',
						    esc_attr( $image_size_option ),
						    selected( $image_size_option, $instance['image_size'], false ),
						    ucfirst( $image_size_option )					    
						);
					}
					?>
				</select>
			</p>	        					
			<p>
				<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order by', 'shcreate' ); ?>
					<select class="widefat" name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
						<option value="date-ASC" <?php selected( $instance['orderby'], 'date-ASC', true); ?>><?php _e( 'Date - Ascending', 'shcreate' ); ?></option>
						<option value="date-DESC" <?php selected( $instance['orderby'], 'date-DESC', true); ?>><?php _e( 'Date - Descending', 'shcreate' ); ?></option>
						<option value="popular-ASC" <?php selected( $instance['orderby'], 'popular-ASC', true); ?>><?php _e( 'Popularity - Ascending', 'shcreate' ); ?></option>
						<option value="popular-DESC" <?php selected( $instance['orderby'], 'popular-DESC', true); ?>><?php _e( 'Popularity - Descending', 'shcreate' ); ?></option>
						<option value="rand" <?php selected( $instance['orderby'], 'rand', true); ?>><?php _e( 'Random', 'shcreate' ); ?></option>
					</select>  
				</label>
			</p>	
			<p>
				<label for="<?php echo $this->get_field_id( 'images_link' ); ?>"><?php _e( 'Link to', 'shcreate' ); ?>
					<select class="widefat" name="<?php echo $this->get_field_name( 'images_link' ); ?>" id="<?php echo $this->get_field_id( 'images_link' ); ?>">
						<option value="image_url" <?php selected( $instance['images_link'], 'image_url', true); ?>><?php _e( 'Instagram Image', 'shcreate' ); ?></option>
						<option value="user_url" <?php selected( $instance['images_link'], 'user_url', true); ?>><?php _e( 'Instagram Profile', 'shcreate' ); ?></option>
						<?php if ( $instance['attachment'] ) : ?>
						<option value="local_image_url" <?php selected( $instance['images_link'], 'local_image_url', true); ?>><?php _e( 'Locally Saved Image', 'shcreate' ); ?></option>
						<?php endif; ?>
						<option value="none" <?php selected( $instance['images_link'], 'none', true); ?>><?php _e( 'None', 'shcreate' ); ?></option>
					</select>  
				</label>
			</p>			
			<p>
				<label  for="<?php echo $this->get_field_id( 'images_number' ); ?>"><?php _e( 'Number of images to show:', 'shcreate' ); ?>
					<select class="widefat" name="<?php echo $this->get_field_name( 'images_number' ); ?>" 
						id="<?php echo $this->get_field_id( 'images_number' ); ?>">
					<?php
						for ($i=1;$i<51;$i++) { 
					?>
						<option value="<?php echo $i;?>" <?php selected ( $instance['images_number'], $i, true);?> >
							<?php echo $i;?></option>
					<?php } ?>
					</select>
					<span><?php _e( 'Limit 20 if the <strong>Source</strong> is Instagram', 'shcreate' ); ?></span>
				</label>
			</p>
			<p>
				<label  for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Columns:', 'shcreate' ); ?>
					<select class="widefat" name="<?php echo $this->get_field_name( 'columns' ); ?>"
						id="<?php echo $this->get_field_id( 'columns' ); ?>">
					<?php for($i=1;$i<7;$i++) { 
					?>
						<option value="<?php echo $i;?>" <?php selected ( $instance['columns'], $i, true);?> >
                            <?php echo $i;?></option>
                    <?php } ?>
                    </select>
				</label>
			</p>			
			<p class="<?php if ( 'instagram' != $instance['source'] ) echo 'hidden'; ?>">
				<label  for="<?php echo $this->get_field_id( 'refresh_hour' ); ?>"><?php _e( 'Check for new images every:', 'shcreate' ); ?>
					<select class="instagram-refresh" name="<?php echo $this->get_field_name( 'refresh_hour' ); ?>"	
						id="<?php echo $this->get_field_id( 'refresh_hour' ); ?>">
					<?php for($i=1;$i<25;$i++) {
					?>
						<option value="<?php echo $i;?>" <?php selected ( $instance['refresh_hour'], $i, true);?> >
                            <?php echo $i;?></option>
                    <?php } ?>
                    </select>
					<span><?php _e('hours', 'shcreate'); ?></span>
				</label>
			</p>
			<div class="jr-advanced-input">
				<div class="jr-image-options">
					<h4 class="jr-advanced-title"><?php _e( 'Optional Image Settings', 'shcreate'); ?></h4>
					<p>
						<label for="<?php echo $this->get_field_id( 'image_link_rel' ); ?>"><?php _e( 'Image Link rel attribute', 'shcreate' ); ?>:</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'image_link_rel' ); ?>" name="<?php echo $this->get_field_name( 'image_link_rel' ); ?>" value="<?php echo $instance['image_link_rel']; ?>" type="text" />
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'image_link_class' ); ?>"><?php _e( 'Image Link class', 'shcreate' ); ?>:</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'image_link_class' ); ?>" name="<?php echo $this->get_field_name( 'image_link_class' ); ?>" value="<?php echo $instance['image_link_class']; ?>" type="text" />
						<span class="jr-description"><?php _e( 'For lightbox plugins to open links', 'shcreate' ); ?></span>

					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Selected array function echoes selected if in array
	 * 
	 * @param  array $haystack The array to search in
	 * @param  string $current  The string value to search in array;
	 * 
	 * @return string
	 */
	public function selected( $haystack, $current ) {
		
		if( is_array( $haystack ) && in_array( $current, $haystack ) ) {
			selected( 1, 1, true );
		}
	}	

	/**
	 * Echoes the Display Instagram Images method
	 * 
	 * @param  array $args
	 * 
	 * @return void
	 */
	public function instagram_images( $args ) {
		echo $this->display_images( $args );
	}

	/**
	 * Runs the query for images and returns the html
	 * 
	 * @param  array  $args 
	 * 
	 * @return string       
	 */
	private function display_images( $args ) {

		$username         = isset( $args['username'] ) && !empty( $args['username'] ) ? $args['username'] : false;
		$source           = isset( $args['source'] ) && !empty( $args['source'] ) ? $args['source'] : 'instagram';
		$attachment       = isset( $args['attachment'] ) ? true : false;
		$template         = 'thumbs';
		$orderby          = isset( $args['orderby'] ) ? $args['orderby'] : 'rand';
		$images_link      = isset( $args['images_link'] ) ? $args['images_link'] : 'local_image_url';
		$images_number    = isset( $args['images_number'] ) ? absint( $args['images_number'] ) : 5;
		$columns          = isset( $args['columns'] ) ? absint( $args['columns'] ) : 4;
		$refresh_hour     = isset( $args['refresh_hour'] ) ? absint( $args['refresh_hour'] ) : 5;
		$image_size       = isset( $args['image_size'] ) ? $args['image_size'] : 'full';
		$image_link_rel   = isset( $args['image_link_rel'] ) ? $args['image_link_rel'] : '';
		$image_link_class = isset( $args['image_link_class'] ) ? $args['image_link_class'] : '';
		$description      = isset( $args['description'] ) ? $args['description'] : array();

		if ( false == $username ) {
			return false;
		}

		if ( !empty( $description ) && !is_array( $description ) ) {
			$description = explode( ',', $description );
		}

		if ( $source == 'instagram' && $refresh_hour == 0 ) {
			$refresh_hour = 5;
		}
		
		$template_args = array(
			'source'      => $source,
			'attachment'  => $attachment,
 			'image_size'  => $image_size,
			'link_rel'    => $image_link_rel,
			'link_class'  => $image_link_class
		);

		$images_div_class = 'sh-insta-thumb';
		$ul_class         = 'thumbnails sh_col_' . $columns;
		$slider_script    = ''; 

		$images_div = "<div class='{$images_div_class}'>\n";
		$images_ul  = "<ul class='no-bullet {$ul_class}'>\n";

		$output = __( 'No saved images for ' . $username, 'shcreate' );
		
		if ( ( $attachment && $source == 'instagram' ) || ( $source == 'media_library') ) {

			$query_args = array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'orderby'		 => 'rand',
				'no_found_rows'  => true
			);
			
			if ( $orderby != 'rand' ) {
				
				$orderby = explode( '-', $orderby );
				$meta_key = $orderby[0] == 'date' ? 'sh_insta_timestamp' : 'sh_insta_popularity';
				
				$query_args['meta_key'] = $meta_key;
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = $orderby[1];
			}
			
			if ( $source != 'instagram' ) {
				$query_args['posts_per_page'] = $images_number;
				$query_args['meta_query'] = array(
					array(
						'key'     => 'sh_insta_username',
						'value'   => $username,
						'compare' => '='
					)
				);

			} else {
				
				$attachment_ids = $this->instagram_data( $username, $refresh_hour, $images_number, true );
				
				if ( is_array( $attachment_ids ) && !empty( $attachment_ids ) ) {
					$query_args['post__in'] = $attachment_ids;
				} else {
					if ( is_array( $attachment_ids ) ) {
						return __( 'Images were not found for this user. This account may not be public.', 'shcreate' );
					} else {
						return $attachment_ids;
					}
				}
			}
			
			$instagram_images = new WP_Query( $query_args );

			if ( $instagram_images->have_posts() ) {
				
				$output = $slider_script . $images_div . $images_ul;
				
				while ( $instagram_images->have_posts() ) : $instagram_images->the_post();
					
					$id = get_the_id();

					if ( 'image_url' == $images_link ) {
						$template_args['link_to'] = get_post_meta( $id, 'sh_insta_link', true );
					} elseif ( 'user_url' == $images_link ) {
						$template_args['link_to'] = 'http://instagram.com/' . $username;
					} elseif ( 'local_image_url' == $images_link ) {
						$template_args['link_to'] = wp_get_attachment_url( $id );
					} elseif ( 'attachment' == $images_link ) {
						$template_args['link_to'] = get_permalink( $id );
					}

					$output .= $this->get_template( $template, $template_args );

				endwhile;

				$output .= "</ul>\n</div>";
			}

			wp_reset_postdata();

		} else {
			
			$images_data = $this->instagram_data( $username, $refresh_hour, $images_number, false );
			
			if ( is_array( $images_data ) && !empty( $images_data ) ) {

				if ( $orderby != 'rand' ) {
					
					$orderby = explode( '-', $orderby );
					$func = $orderby[0] == 'date' ? 'sort_timestamp_' . $orderby[1] : 'sort_popularity_' . $orderby[1];
					
					usort( $images_data, array( $this, $func ) );

				} else {
					
					shuffle( $images_data );
				}				
				
				$output = $slider_script . $images_div . $images_ul;

				foreach ( $images_data as $image_data ) {
					
					if ( 'image_url' == $images_link ) {
						$template_args['link_to'] = $image_data['link'];
					} elseif ( 'user_url' == $images_link ) {
						$template_args['link_to'] = 'http://instagram.com/' . $username;
					}

					if ( $image_size == 'thumbnail' ) {
						$template_args['image'] = $image_data['url_thumbnail'];
					} elseif ( $image_size == 'medium' ) {
						$template_args['image'] = $image_data['url_medium'];
					} elseif( $image_size == 'large' ) {
						$template_args['image'] = $image_data['url'];
					} else {
						$template_args['image'] = $image_data['url'];
					}

					$template_args['caption']   = $image_data['caption'];
					$template_args['timestamp'] = $image_data['timestamp'];
					$template_args['username']  = $image_data['username'];
					
					$output .= $this->get_template( $template, $template_args );
				}

				$output .= "</ul>\n</div>";
			}
		}			
		
		return $output;
		
	}

	/**
	 * Function to display Templates styles
	 *
	 * @param    string    $template
	 * @param    array	   $args	    
	 *
	 * return mixed
	 */
	private function get_template( $template, $args ) {

		$link_to   = isset( $args['link_to'] ) ? $args['link_to'] : false;
		
		if ( $args['attachment'] !== true && $args['source'] == 'instagram' ) {
			$caption   = $args['caption'];
			$time      = $args['timestamp'];
			$username  = $args['username'];
			$image_url = $args['image'];
		} else {
			$attach_id = get_the_id();
			$caption   = get_the_excerpt();
			$time      = get_post_meta( $attach_id, 'sh_insta_timestamp', true );
			$username  = get_post_meta( $attach_id, 'sh_insta_username', true );
			$image_url = wp_get_attachment_image_src( $attach_id, $args['image_size'] );
			$image_url = $image_url[0];
		}

		$short_caption = wp_trim_words( $caption, 10 );

		$image_src = '<img src="' . $image_url . '" alt="' . $short_caption . '" title="' . $short_caption . '" />';
		$image_output  = $image_src;

		if ( $link_to ) {
			$image_output  = '<a href="' . $link_to . '" target="_blank"';

			if ( ! empty( $args['link_rel'] ) ) {
				$image_output .= ' rel="' . $args['link_rel'] . '"';
			}

			if ( ! empty( $args['link_class'] ) ) {
				$image_output .= ' class="' . $args['link_class'] . '"';
			}
			$image_output .= ' title="' . $short_caption . '">' . $image_src . '</a>';
		}		

		$output = '';
		
		$output .= "<li>";
		$output .= $image_output;
		$output .= "</li>";
		return $output;
	}	
	
	/**
	 * Stores the fetched data from instagram in WordPress DB using transients
	 *	 
	 * @param    string    $username    	Instagram Username to fetch images from
	 * @param    string    $cache_hours     Cache hours for transient
	 * @param    string    $nr_images    	Nr of images to fetch from instagram		  	 
	 *
	 * @return array of localy saved instagram data
	 */
	private function instagram_data( $username, $cache_hours, $nr_images, $attachment ) {
		
		$opt_name  = 'sh_insta_' . md5( $username );
		$instaData = get_transient( $opt_name );
		$user_opt  = (array) get_option( $opt_name );
		
		if ( false === $instaData || $user_opt['username'] != $username || $user_opt['cache_hours'] != $cache_hours || $user_opt['nr_images'] != $nr_images || $user_opt['attachment'] != $attachment ) {
			
			$instaData = array();
						
			$user_opt['username']    = $username;
			$user_opt['cache_hours'] = $cache_hours;
			$user_opt['nr_images']   = $nr_images;
			$user_opt['attachment']  = $attachment;

			$response = wp_remote_get( 'http://instagram.com/' . trim( $username ), array( 'sslverify' => false, 'timeout' => 60 ) );

			if ( is_wp_error( $response ) ) {

				return $response->get_error_message();
			}
			
			if ( $response['response']['code'] == 200 ) {
				
				$json = str_replace( 'window._sharedData = ', '', strstr( $response['body'], 'window._sharedData = ' ) );
				
				// Compatibility for version of php where strstr() doesnt accept third parameter
				if ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
					$json = strstr( $json, '</script>', true );
				} else {
					$json = substr( $json, 0, strpos( $json, '</script>' ) );
				}
				
				$json = rtrim( $json, ';' );
				
				// Function json_last_error() is not available before PHP * 5.3.0 version
				if ( function_exists( 'json_last_error' ) ) {
					
					( $results = json_decode( $json, true ) ) && json_last_error() == JSON_ERROR_NONE;
					
				} else {
					
					$results = json_decode( $json, true );
				}
				
				if ( $results && is_array( $results ) ) {

					$userMedia = isset( $results['entry_data']['UserProfile'][0]['userMedia'] ) ? $results['entry_data']['UserProfile'][0]['userMedia'] : array();
					
					if ( empty( $userMedia ) ) {
						return __( 'No images found', 'shcreate');
					}

					foreach ( $userMedia as $current => $result ) {
						
						if ( $result['type'] != 'image' ) {
							$nr_images++;
							continue;
						}
						
						if ( $current >= $nr_images ) {
							break;
						}
						
						$image_data['username']      = $result['user']['username'];
						$image_data['caption']       = $this->sanitize( $result['caption']['text'] );
						$image_data['id']            = $result['id'];
						$image_data['link']          = $result['link'];
						$image_data['popularity']    = (int) ( $result['comments']['count'] ) + ( $result['likes']['count'] );
						$image_data['timestamp']     = (int) $result['created_time'];
						$image_data['url']           = $result['images']['standard_resolution']['url'];
						$image_data['url_thumbnail'] = $result['images']['thumbnail']['url'];
						$image_data['url_medium']    = $result['images']['low_resolution']['url'];

						if ( !$attachment ) {
							
							$instaData[] = $image_data;
						
						} else {
						
							if ( isset( $user_opt['saved_images'][$image_data['id']] ) ) {
								
								if ( is_string( get_post_status( $user_opt['saved_images'][$image_data['id']] ) ) ) {
									
									$this->update_wp_attachment( $user_opt['saved_images'][$image_data['id']], $image_data );
									
									$instaData[$image_data['id']] = $user_opt['saved_images'][$image_data['id']];
								
								}  else {

									$user_opt['deleted_images'][$image_data['id']] = $image_data['url_thumbnail'];
								}
								
							} else {
								
								$id = $this->save_wp_attachment( $image_data );
								
								if ( $id && is_numeric( $id ) ) {
									
									$user_opt['saved_images'][$image_data['id']] = $id;
									
									$instaData[$image_data['id']] = $id;
								
								} else {

									return $id;
								}
								
							} // end isset $saved_images

						} // false to save attachments
						
					} // end -> foreach
					
				} // end -> ( $results ) && is_array( $results ) )
				
			} else { 

				return $response['response']['message'];

			} // end -> $response['response']['code'] === 200 )

			update_option( $opt_name, $user_opt );
			
			if ( is_array( $instaData ) && !empty( $instaData )  ) {

				set_transient( $opt_name, $instaData, $cache_hours * 60 * 60 );
			}
			
		} // end -> false === $instaData
		
		return $instaData;
	}

	/**
	 * Updates attachment using the id
	 * @param     int      $attachment_ID
	 * @param     array    image_data
	 * @return    void
	 */
	private function update_wp_attachment( $attachment_ID, $image_data ) {
		
		update_post_meta( $attachment_ID, 'sh_insta_popularity', $image_data['popularity'] );
	}
	
	/**
	 * Save Instagram images to upload folder and ads to media.
	 * If the upload fails it returns the remote image url. 
	 *
	 * @param    string    $url    		Url of image to download
	 * @param    string    $file    	File path for image	
	 *
	 * @return   string    $url 		Url to image
	 */
	private function save_wp_attachment( $image_data ) {
		
		$image_info = pathinfo( $image_data['url'] );
		
		if ( !in_array( $image_info['extension'], array( 'jpg', 'jpe', 'jpeg', 'gif', 'png' ) ) ) {
			return false;
		}
		
		// These files need to be included as dependencies when on the front end.
		if( !function_exists( 'download_url' ) || !function_exists( 'media_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		$tmp = download_url( $image_data['url'] );
		
		$file_array             = array();
		$file_array['name']     = $image_info['basename'];
		$file_array['tmp_name'] = $tmp;
		
		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			
			@unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';

			return $tmp->get_error_message();
		}
		
		$id = media_handle_sideload( $file_array, 0, NULL, array(
			'post_excerpt' => $image_data['caption'] 
		) );
		
		// If error storing permanently, unlink
		if ( is_wp_error( $id ) ) {

			@unlink( $file_array['tmp_name'] );
			
			return $id->get_error_message();
		}
		
		unset( $image_data['caption'] );
		
		foreach ( $image_data as $meta_key => $meta_value ) {
			update_post_meta( $id, 'sh_insta_' . $meta_key, $meta_value );
		}
		
		return $id;
	}

	/**
	 * Add new attachment Description only for instgram images
	 * 
	 * @param  array $form_fields
	 * @param  object $post
	 * 
	 * @return array
	 */
	public function insta_attachment_fields( $form_fields, $post ) {
		
		$instagram_username = get_post_meta( $post->ID, 'sh_insta_username', true );
		
		if ( !empty( $instagram_username ) ) {
			
			$form_fields["sh_insta_username"] = array(
				"label" => __( "Instagram Username", 'shcreate' ),
				"input" => "html",
				"html"  => "<span style='line-height:31px'><a target='_blank' href='http://instagram.com/{$instagram_username}'>{$instagram_username}</a></span>"
			);

			$instagram_link = get_post_meta( $post->ID, 'sh_insta_link', true );		
			if ( !empty( $instagram_link ) ) {
				$form_fields["sh_insta_link"] = array(
					"label" => __( "Instagram Image", "shcreate" ),
					"input" => "html",
					"html"  => "<span style='line-height:31px'><a target='_blank' href='{$instagram_link}'>{$instagram_link}</a></span>"
				);
			}

			$instagram_date = get_post_meta( $post->ID, 'sh_insta_timestamp', true );
			if ( !empty( $instagram_date ) ) {
				$instagram_date = date( "F j, Y, g:i a", $instagram_date );
				$form_fields["sh_insta_time"] = array(
					"label" => __( "Posted on Instagram", "shcreate" ),
					"input" => "html",
					"html"  => "<span style='line-height:31px'>{$instagram_date}</span>"
				);
			}				
		}

		return $form_fields;
	}

	/**
	 * Sort Function for timestamp Ascending
	 */
	public function sort_timestamp_ASC( $a, $b ) {
		return $a['timestamp'] > $b['timestamp'];
	}

	/**
	 * Sort Function for timestamp Descending
	 */
	public function sort_timestamp_DESC( $a, $b ) {
		return $a['timestamp'] < $b['timestamp'];
	}

	/**
	 * Sort Function for popularity Ascending
	 */
	public function sort_popularity_ASC( $a, $b ) {
		return $a['popularity'] > $b['popularity'];
	}

	/**
	 * Sort Function for popularity Descending
	 */
	public function sort_popularity_DESC( $a, $b ) {
		return $a['popularity'] < $b['popularity'];
	}

	/**
	 * Action function when user deletes an attachment
	 * @param  int $post_id
	 * @return void
	 */
	public function delete_wp_attachment( $post_id ) {
		
		$username = get_post_meta( $post_id, 'sh_insta_username', true );
		
		if ( !empty( $username ) ) {
			delete_transient( 'sh_insta_' . md5( $username ) );
		}
	}

	/**
	 * Ajax Call to unblock images
	 * @return void
	 */
	public function unblock_images() {
		if (function_exists('check_ajax_referer')) {
			check_ajax_referer( 'jr_unblock_instagram_image' );
		}

		$post = $_POST;
		$option_id    = 'sh_insta_' . md5( $post['username'] );
		$user_options = get_option( $option_id );

		unset( $user_options['deleted_images'][$post['id']] );
		unset( $user_options['saved_images'][$post['id']] );
		
		update_option( $option_id, $user_options );
		delete_transient( $option_id );
		
		die('success');	
	}

	/**
	 * Sanitize 4-byte UTF8 chars; no full utf8mb4 support in drupal7+mysql stack.
	 * This solution runs in O(n) time BUT assumes that all incoming input is
	 * strictly UTF8.
	 *
	 * @param    string    $input 		The input to be sanitised
	 *
	 * @return the sanitized input
	 */
	private function sanitize( $input ) {
				
		if ( !empty( $input ) ) {
			$utf8_2byte       = 0xC0 /*1100 0000*/ ;
			$utf8_2byte_bmask = 0xE0 /*1110 0000*/ ;
			$utf8_3byte       = 0xE0 /*1110 0000*/ ;
			$utf8_3byte_bmask = 0XF0 /*1111 0000*/ ;
			$utf8_4byte       = 0xF0 /*1111 0000*/ ;
			$utf8_4byte_bmask = 0xF8 /*1111 1000*/ ;
			
			$sanitized = "";
			$len       = strlen( $input );
			for ( $i = 0; $i < $len; ++$i ) {
				$mb_char = $input[$i]; // Potentially a multibyte sequence
				$byte    = ord( $mb_char );
				
				if ( ( $byte & $utf8_2byte_bmask ) == $utf8_2byte ) {
					$mb_char .= $input[++$i];
				} else if ( ( $byte & $utf8_3byte_bmask ) == $utf8_3byte ) {
					$mb_char .= $input[++$i];
					$mb_char .= $input[++$i];
				} else if ( ( $byte & $utf8_4byte_bmask ) == $utf8_4byte ) {
					// Replace with ? to avoid MySQL exception
					$mb_char = '';
					$i += 3;
				}
				$sanitized .= $mb_char;
			}
			$input = $sanitized;
		}
		return $input;
	}
	
} // end of class
