<?php
/**
  * Initialize Theme Wordpress Init
  */

	class themeInit {
		// properties

		// methods
		public function __construct() {
			//add_action('init', array($this, 'register_posts'));  // Moved to sh-shortcodes plugin
			add_action('widgets_init', array($this, 'register_widgets'));
			// Add Top search function
            add_filter('wp_nav_menu_items', array( $this, 'add_search_box_to_menu'), 20, 2);
			// Add Styled Language select for wpml
			add_filter('wp_nav_menu_items', array( $this, 'add_wpml_to_menu'), 20, 2);

			// Include any custom widgets
			require_once get_template_directory() . '/theme/widgets/theme-like-widget.php';
			require_once get_template_directory() . '/theme/widgets/twitter-widget.php';
			require_once get_template_directory() . '/theme/widgets/flickr-widget.php';
			require_once get_template_directory() . '/theme/widgets/theme-login-widget.php';
			require_once get_template_directory() . '/theme/widgets/portfolio-widget.php';
			require_once get_template_directory() . '/theme/widgets/ads-widget.php';
			require_once get_template_directory() . '/theme/widgets/tab-widget.php';
			require_once get_template_directory() . '/theme/widgets/instagram-widget.php';




			// Include our post like system
			require_once get_template_directory() . '/theme/post-like.php';

			// Include nav menu images capabilities
			require_once get_template_directory() . '/theme/nav-menu-images/nav-menu-images.php';

         	// Woo Commerce Support
        	add_theme_support( 'woocommerce' );
			// Include customized woocommerce items
			require_once get_template_directory() . '/theme/theme-commerce.php';

			// Add image size formats options for portfolio and blog
			add_image_size( 'panoramic', 900, 300, true); // 3:1
			add_image_size( 'square', 500, 500, true);    // 1:1
			add_image_size( 'standard', 800, 600, true);  // 4:3
			add_image_size( 'widescreen', 800, 450, true);// 16:9
			add_image_size( 'tall', 500, 750, true);     // 2:3

			// Dynamic frontend css, enqueued in theme-settings
        	add_action('wp_ajax_shdynamic_css', array($this, 'dynamic_css'));
        	add_action('wp_ajax_nopriv_shdynamic_css', array($this, 'dynamic_css'));
		}

		/*
     	 * Theme dynamic css function
     	 */
    	public function dynamic_css() {
        	get_template_part( 'css/dynamic-style.css' );
        	wp_die();
    	}

		public function add_search_box_to_menu($items, $args) {
			global $shcreate;
			$search = '';
			/* Theme overrides for pages */
			$page_id = get_queried_object_id();
			$meta    = get_post_meta($page_id);
			$classic = false;
			if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
            	if ($meta['menubar'][0] == 'classic') {
                	$classic = true;
                }
            } elseif ($shcreate['nav-option'] == 1) { // Classic menu add to main nav
					$classic = true;
			}
			if (true == $classic) {
				if ($args->theme_location == 'nav-menu' && $shcreate['menu-search-option'] == '1') {  // If it's enabled
					$search = '<li class="menu-item"><a class="top-search dropdown-toggle" data-toggle="dropdown" href="#">'
                        . '<i class="fa fa-search"></i></a></li>';
				}
			} else { // stacked versions - secondary menu
				if ($args->theme_location == 'nav-menu-secondary' && $shcreate['menu-search-option'] == '1') {
					$search = '<li class="menu-item"><a class="top-search dropdown-toggle" data-toggle="dropdown" href="#">'
							. '<i class="fa fa-search"></i></a></li>';
				}
			}
			return $items.$search;
		}

		public function add_wpml_to_menu($items, $args) {
			global $shcreate;
			$icl = '';
			/* Theme overrides for pages */
            $page_id = get_queried_object_id();
            $meta    = get_post_meta($page_id);
            $classic = false;
			if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
               	if ($meta['menubar'][0] == 'classic') {
                   	$classic = true;
               	}
            } elseif ($shcreate['nav-option'] == 1) { // Classic menu add to main nav
                $classic = true;
           	}
           	if (true == $classic) {
				$menu_sel = 'nav-menu'; // primary
			} else {
				$menu_sel = 'nav-menu-secondary';
			}

			if ($args->theme_location == $menu_sel && $shcreate['menu-language-select'] == '1') { // enabled
				$existing_languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0) );
				$count = count($existing_languages);
				// loop twice to get the current language first and then the other languages to build the ul
				if ($count) {
					foreach( $existing_languages as $lang ){
						if ($lang['active']) {
							$extra_class = ' class="menu-item dropdown icl-menu"';
							$icl .= sprintf( '<li' . "%s" . '><a href="' . "%s" . '" class="dropdown-toggle"'
									. ' data-toggle="dropdown">'
									. '<span class="nav-link-href"><img src="' . "%s" . '" alt="' . "%s" . '">%s</span>'
									. ' <span class="nav-link-down fa fa-angle-down"> </span></a>',
              					$extra_class,
               					$lang['url'],
								$lang['country_flag_url'],
               					$lang['native_name'],
               					$lang['native_name']
           					);
							if ($count > 1) {  // Only create the dropdown if there is more than the current language
								$icl .= '<ul class="dropdown-menu" role="menu">';
							}
						}
					}
					foreach ( $existing_languages as $lang ) {
						if (!$lang['active']) {
							$icl .= sprintf( '<li class="menu-item"><a href="' . "%s" . '"><img src="' . "%s" . '" alt="' 
									. "%s" . '">' . "%s" . '</a></li>',
								$lang['url'],
								$lang['country_flag_url'],
								$lang['native_name'],
								$lang['native_name']
							);
						}
					}
					if ($count > 1) {
						$icl .= '</ul>';
					}
					$icl .= '</li>';	
				}
			}
			return $items.$icl; 
		}

		/**
		 * Register Sidebar Widgets
		 */
		public function register_widgets() {
			/* Global shcreate is not registering at this point (for some reason) so we access the settings directly 
               would be best to use the global $shcreate but this works fine too, just another db call.
            */
            $settings = get_option( 'shcreate' );

			// SideBar
        	if ( function_exists('register_sidebar') ) {
            	register_sidebar(array(
                	'name' => 'Sidebar',
					'id' => 'sidebar-1',
                	'before_widget' => '<aside class="widget-section"> <div id="%1$s" class="widgetSidebar %2$s">',
                	'after_widget' => '</div></aside>',
                	'before_title' => '<h6>',
                	'after_title' => '</h6><div class="sidebar-section"></div>',
            	));
        	}

        	// Footer
        	if ( function_exists('register_sidebar') ) {
				$columns = isset($settings['footerw-columns']) ? $settings['footerw-columns'] : '4';
				if ($columns == '4') {
					$colNum = '3';
					$colSmall = '6';
				} elseif ($columns == '3') {
					$colNum = '4';
					$colSmall = '6';
				} elseif ($columns == '2') {
					$colNum = '6';
					$colSmall = '12';
				} elseif ($columns == '1') {
					$colNum = '12';
					$colSmall = '12';
				}
            	register_sidebar(array(
                	'name' => 'Footer',
					'id' => 'footerarea',
                	'before_widget' => '<div class="col-sm-' . $colSmall . ' col-md-' . $colSmall 
						. ' col-lg-' . $colNum . '"> <div id="%1$s" class="footer-widget %2$s">',
                	'after_widget' => '</div></div>',
                	'before_title' => '<h6>',
                	'after_title' => '</h6>',
            	));
        	}

            if ( function_exists('register_sidebar') ) {
                $columns = 'col-sm-6 col-md-3'; // default 4 columns
                if ( $settings['top-slider-columns'] == '1') { $columns = 'col-md-12'; }
                elseif ( $settings['top-slider-columns'] == '2') { $columns = 'col-md-6'; }
                elseif ( $settings['top-slider-columns'] == '3') { $columns = 'col-md-4'; }
                register_sidebar(array(
                    'name' => 'Topslide',
					'id' => 'topslide',
                    'before_widget' => '<div class="' . $columns . '"> <div id="%1$s" class="topslide-widget %2$s">',
                    'after_widget' => '</div></div>',
                    'before_title' => '<h6>',
                    'after_title' => '</h6>',
                ));
            }

            // Woo Commerce
            if ( function_exists('is_woocommerce') && function_exists('register_sidebar') ) {
                register_sidebar(array( 
                    'name' => 'Woocommerce',
                    'id' => 'woocommerce',
                    'before_widget' => '<aside class="widget-section"> <div id="%1$s" class="widgetSidebar %2$s">',
                    'after_widget' => '</div></aside>',
                    'before_title' => '<h5>',
                    'after_title' => '</h5><div class="sidebar-section"></div>',
                ));
            }


			// Allow shortcodes in widgets
        	add_filter('widget_text', 'do_shortcode');
		}
	}
