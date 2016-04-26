<?php

class CTImport extends WP_Import {
	protected $importer;
	public $site_url;
	public $demo_url;
	public $demo_file;
	public $demo_options;
	public $demo_widget_file;
	public $sidebar_file;
    public $success;
	public $options_success;
	public $widget_success;
    public $response;
	protected $dummyImg = 0;

	public function __construct() {
	}

	/** 
	  * Get the different demos and associated files
	  * 1 credence main
	  * 2 credence single
	  * 3 credence dark
	  * 4 credence blog
	  *********************
	  * Sidebars and Options are direct copy from database, widget export uses plugin (Widget Data - Setting Import/Export Plugin)
	  * and then of course our general export file
	**/
	public function getDemo($demo_choice) {
		// testing responses
		//$response = array();
		//$response['type'] = 'success';
		//$response['type'] = 'fail';
		//echo json_encode($response);
		//die();

        $this->site_url = get_site_url();
        if ($demo_choice == 1) {  // Credence Main Demo
            $this->demo_url         = 'http://demo.sh-themes.com/credencebeta';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-main.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-main.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-main.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-main.json';
        } elseif ($demo_choice == 2) { // Credence Single Page Demo
            $this->demo_url         = 'http://demo.sh-themes.com/credencesingle';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-single.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-single.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-single.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-single.json';
        } elseif ($demo_choice == 4) { // Credence Blog Demo
            $this->demo_url         = 'http://demo.sh-themes.com/credenceblog';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-blog.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-blog.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-blog.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-blog.json';
        } elseif ($demo_choice == 5) { // Credence CV / Resume Demo
			$this->demo_url         = 'http://demo.sh-themes.com/credencecv';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-cv.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-cv.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-cv.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-cv.json';
		} elseif ($demo_choice == 6) { // Credence Bistro / Restaurant Demo
			$this->demo_url         = 'http://demo.sh-themes.com/credencebistro';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-bistro.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-bistro.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-bistro.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-bistro.json';
		} elseif ($demo_choice == 7) { // Credence Service / Builder Demo
            $this->demo_url         = 'http://demo.sh-themes.com/credenceservice';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-builder.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-builder.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-builder.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-builder.json';
        } elseif ($demo_choice == 8) { // Credence Yoga & Fitness Demo
            $this->demo_url         = 'http://demo.sh-themes.com/credenceyoga';
            $this->demo_file        = dirname(__FILE__) . '/inc/imports/credence-yoga.xml';
            $this->demo_options     = dirname(__FILE__) . '/inc/imports/options-yoga.json';
            $this->sidebar_file    = dirname(__FILE__) . '/inc/imports/sidebar-yoga.txt';
            $this->demo_widget_file = dirname(__FILE__) . '/inc/imports/widget-yoga.json';
        }




		$this->success = false;
		$this->options_success = false;
		$this->widget_success = false;
        $this->response = "";

		set_time_limit(0);

        ob_start();
        add_action("import_end",array(&$this,"import_end"));
        add_action("wp_insert_post",array(&$this,"update_progress"));
		$this->import($this->demo_file);  // calls parent import

		// Widget and Sidebars files from demo
		if (file_exists($this->demo_widget_file)) {
			if (file_exists($this->sidebar_file)) {  // create the initial sidebars from sidebar_data.txt 
													 // (manual export of sidebars_widgets)
				$sidebar_data = unserialize(file_get_contents($this->sidebar_file));
				update_option( 'sidebars_widgets', $sidebar_data );
			}
			$parsedFile = $this->import_widget_file($this->demo_widget_file);
			$this->widget_success = true;
		}
		// Redux Options configurations from demo
        if (file_exists($this->demo_options)) {
            global $framework_config;
            $file_contents = file_get_contents($this->demo_options);
            $options = json_decode($file_contents, true);
            foreach ($options as $key => $value) {
                // filter and reset key fields that need updating
                $value = str_replace($this->demo_url, $this->site_url, $value);
                if ( $key == 'style-switcher'
                    || $key == 'update-mojo-code'
                    || $key == 'twit-consumerkey'
                    || $key == 'twit-consumersecret'
                    || $key == 'twit-accesstoken'
                    || $key == 'twit-accesstokensecret'
					|| $key == 'google-map-key'
                ) {
                    $value = '';
                }
                $framework_config->ReduxFramework->set($key, $value);
            }
        }

		/* Pre-set home page and menus after other items are done 
		 * Also add sliders if LayerSlider and RevSlider are active
         * currently just for main demo
         */
        if ($demo_choice == 1) {
            global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="home"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
            // set menu locations
            $primary_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="navigation"');
            $secondary_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="secondary-navigation"');
            $top_minor_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="top-layered-minor"');
            $off_canvas_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="side-menu"');
            $footer_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="footer"');

            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => $primary_id,
                    'top-minor-menu'     => $top_minor_id,
                    'nav-menu-secondary' => $secondary_id,
                    'main-side-menu'     => '',
                    'side-menu'          => $off_canvas_id,
                    'footer-menu'        => $footer_id
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks

			// Sliders - LayerSlider
			if (defined('LS_PLUGIN_VERSION')) {  // make sure it's installed and active
				include LS_ROOT_PATH . '/classes/class.ls.importutil.php';
				ob_start();
        		$import = new LS_ImportUtil(get_template_directory() 
						. '/theme/admin/inc/plugin-imports/LayerSlider_demo-slider.zip');
				ob_end_clean();
			}
			// Sliders - RevSlider
			if ( class_exists( 'RevSlider' ) ) { // make sure it's installed and active
				$slider_array = array(
									get_template_directory() . "/theme/admin/inc/plugin-imports/shop-slider.zip",
									get_template_directory() . "/theme/admin/inc/plugin-imports/home-2-slider.zip",
								);
				$slider = new RevSlider();
				foreach($slider_array as $filepath){
					ob_start();
					$slider->importSliderFromPost(true,true,$filepath);  
					ob_end_clean();
				}
			}

        } // end update front page and menu setup (Main Demo)

		/* Demo 2 single page scroll */
		if ($demo_choice == 2) {
            global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_title="Single-Home"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
			// set menu locations
            $primary_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="main"');
			$menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => $primary_id,
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => '',
                    'side-menu'          => '',
                    'footer-menu'        => ''
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
		} // End update demo2 single page scroll

		if ($demo_choice == 4) { // Credence Blog Demo
			global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="home" and post_type="page"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
			// set menu locations
            $primary_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="main-menu"');
			$footer_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="footer"');
            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => $primary_id,
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => '',
                    'side-menu'          => '',
                    'footer-menu'        => $footer_id
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
		} // End Blog Setup

		if ($demo_choice == 5) { // Credence CV / Resume Demo
			global $wpdb;
			$home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="cvhome" and post_type="page"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
			// set menu locations
            $main_side_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="cv-main-side-menu"');
            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => '',
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => $main_side_id,
                    'side-menu'          => '',
                    'footer-menu'        => ''
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
        } // End CV setup

		if ($demo_choice == 6) { // Credence Bistro / Restaurant Demo
			global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="home" and post_title="Home-Restaurant"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
            // set menu locations
            $main_side_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="main-side-menu"');
            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => '',
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => $main_side_id,
                    'side-menu'          => '',
                    'footer-menu'        => ''
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
        }  // End Bistro setup

		if ($demo_choice == 7) { // Credence Service / Builder Demo
            global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="services-home" and post_type="page"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
            // set menu locations
            $main_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="home-builder-main"');
            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => $main_id,
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => '',
                    'side-menu'          => '',
                    'footer-menu'        => ''
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
        } // End Service setup
	
		if ($demo_choice == 8) { // Credence Yoga & Fitness Demo
            global $wpdb;
            $home_id = $wpdb->get_var( 'select ID from ' . $wpdb->prefix . 'posts WHERE post_name="home-fitness" and post_type="page"');
            // set front page
            if ($home_id) {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_id);
            }
            // set menu locations
            $main_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="yoga-main-menu"');
			$footer_id = $wpdb->get_var( 'select term_id from ' . $wpdb->prefix  . 'terms WHERE slug="yoga-footer"');
            $menu_locations = array(
                'nav_menu_locations' => array(
                    'nav-menu'           => $main_id,
                    'top-minor-menu'     => '',
                    'nav-menu-secondary' => '',
                    'main-side-menu'     => '',
                    'side-menu'          => '',
                    'footer-menu'        => $footer_id
                )
            );
            $theme = wp_get_theme();
            $theme = strtolower($theme);
            update_option("theme_mods_$theme", $menu_locations);
            update_option('permalink_structure', '/%postname%/');  // pretty permalinks
			
			// Sliders - LayerSlider
            if (defined('LS_PLUGIN_VERSION')) {  // make sure it's installed and active
                include LS_ROOT_PATH . '/classes/class.ls.importutil.php';
                ob_start();
                $import = new LS_ImportUtil(get_template_directory() 
                        . '/theme/admin/inc/plugin-imports/Yoga_Slider.zip');
                ob_end_clean();
            }
        } // End Yoga setup
			
	}

	/**
	 *	Import Widget data
	**/
	public function import_widget_file($import_file) {

		if( empty($import_file) ){
			return;
			//$response['id'] = new WP_Error('import_widget_data', 'No widget data posted to import');
			//$response = new WP_Ajax_Response( $response );
			//$response->send();
		}

		$json_data = file_get_contents( $import_file );
		$json_data = json_decode( $json_data, true );
		$sidebar_data = $json_data[0];
		$widget_data = $json_data[1];

		// build widgets list to compare since this would normally come from post data
        $widgets = array();
		if (isset($json_data[0])) :
			foreach ( $this->order_sidebar_widgets( $json_data[0] ) as $sidebar_name => $widget_list ) :
				if ( count( $widget_list ) == 0 ) {
					continue;
				}
				$sidebar_info = $this->get_sidebar_info( $sidebar_name );
				if ( $sidebar_info ) :
					foreach ( $widget_list as $widget ) :
						$widget_options = false;

						$widget_type = trim( substr( $widget, 0, strrpos( $widget, '-' ) ) );
						$widget_type_index = trim( substr( $widget, strrpos( $widget, '-' ) + 1 ) );
						foreach ( $json_data[1] as $name => $option ) {
							if ( $name == $widget_type ) {
								$widget_type_options = $option;
								break;
							}
						}
						if ( !isset($widget_type_options) || !$widget_type_options ) {
							continue;
						}
						$widget_title = isset( $widget_type_options[$widget_type_index]['title'] ) 
							? $widget_type_options[$widget_type_index]['title'] : '';
						$widget_options = $widget_type_options[$widget_type_index];
						$widgets[$widget_type][$widget_type_index] = 'on';
						//$widgets[] = esc_attr( sprintf("widgets[%s][%d]", $widget_type, $widget_type_index) );
					endforeach;
				endif;
			endforeach;
		endif;
		//print_r($widgets);


		foreach ( $sidebar_data as $title => $sidebar ) {
			$count = count( $sidebar );
			for ( $i = 0; $i < $count; $i++ ) {
				$widget = array( );
				$widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
				$widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
				if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
					unset( $sidebar_data[$title][$i] );
				}
			}
			$sidebar_data[$title] = array_values( $sidebar_data[$title] );
		}

		foreach ( $widgets as $widget_title => $widget_value ) {
			foreach ( $widget_value as $widget_key => $widget_value ) {
				$widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
		$this->widget_success = ( $this->parse_import_data( $sidebar_data ) ) ? true : new WP_Error( 'widget_import_submit', 'Unknown Error' );
		//$response = new WP_Ajax_Response( $response );
		//$response->send();
	}

	/**
	 * Import widgets
	 * @param array $import_array
	 */
	public function parse_import_data( $import_array ) {
		$sidebars_data = $import_array[0];
		$widget_data = $import_array[1];
		$current_sidebars = get_option( 'sidebars_widgets' );
		$new_widgets = array( );

		foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

			foreach ( $import_widgets as $import_widget ) :
				//if the sidebar exists
				if ( isset( $current_sidebars[$import_sidebar] ) ) :
					$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
					$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
					$current_widget_data = get_option( 'widget_' . $title );
					$new_widget_name = $this->get_new_widget_name( $title, $index );
					$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

					if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
						while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
							$new_index++;
						}
					}
					$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
					if ( array_key_exists( $title, $new_widgets ) ) {
						$new_widgets[$title][$new_index] = $widget_data[$title][$index];
						$multiwidget = $new_widgets[$title]['_multiwidget'];
						unset( $new_widgets[$title]['_multiwidget'] );
						$new_widgets[$title]['_multiwidget'] = $multiwidget;
					} else {
						$current_widget_data[$new_index] = $widget_data[$title][$index];
						$current_multiwidget = isset($current_widget_data['_multiwidget']) ? $current_widget_data['_multiwidget'] : '';
						$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
						$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
						unset( $current_widget_data['_multiwidget'] );
						$current_widget_data['_multiwidget'] = $multiwidget;
						$new_widgets[$title] = $current_widget_data;
					}

				endif;
			endforeach;
		endforeach;

		if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
			update_option( 'sidebars_widgets', $current_sidebars );

			foreach ( $new_widgets as $title => $content ) {
				$content = apply_filters( 'widget_data_import', $content, $title );
				update_option( 'widget_' . $title, $content );
			}
			return true;
		}
		return false;
	}

	/* 
		Widget import order sidebar widgets 
		called in import_widget_file
	*/
	public function order_sidebar_widgets( $sidebar_widgets ) {
		$inactive_widgets = false;
		//seperate inactive widget sidebar from other sidebars so it can be moved to the end of the array, if it exists
		if ( isset( $sidebar_widgets['wp_inactive_widgets'] ) ) {
			$inactive_widgets = $sidebar_widgets['wp_inactive_widgets'];
			unset( $sidebar_widgets['wp_inactive_widgets'] );
			$sidebar_widgets['wp_inactive_widgets'] = $inactive_widgets;
		}
		return $sidebar_widgets;
	}

	/**
	 *
	 * @param string $widget_name
	 * @param string $widget_index
	 * @return string
	 */
	public function get_new_widget_name( $widget_name, $widget_index ) {
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}

	/**
	 *
	 * @global type $wp_registered_sidebars
	 * @param type $sidebar_id
	 * @return boolean
	 */
	public function get_sidebar_info( $sidebar_id ) {
		global $wp_registered_sidebars;

		//since wp_inactive_widget is only used in widgets.php
		if ( $sidebar_id == 'wp_inactive_widgets' )
			return array( 'name' => 'Inactive Widgets', 'id' => 'wp_inactive_widgets' );

		foreach ( $wp_registered_sidebars as $sidebar ) {
			if ( isset( $sidebar['id'] ) && $sidebar['id'] == $sidebar_id )
				return $sidebar;
		}

		return false;
	}
	

	/**
	 * called by wp importer on successful import
	 **/
    public function import_end() {
        $this->response = ob_get_contents();
        $this->success = true;
        ob_end_clean();
	}

	public function update_progress() {
        if ($this->importer) {
            $this->importer->updateStats();
        }
    }

}

