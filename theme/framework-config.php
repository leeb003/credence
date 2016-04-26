<?php

	class Redux_Framework_config {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;
		private $user;

		public $credence_defaults = array();

		public function __construct( ) {
			// Disable tracking if not set
            $redux_tracking = get_option('redux-framework-tracking');
            if ($redux_tracking != 'no') {
                $redux_tracking['allow_tracking'] = 'no';
                update_option('redux-framework-tracking', $redux_tracking);
            }

			// Get the user
			$this->_current_user = wp_get_current_user();

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();
			
			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( !isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}

			/* 
			 * We need default options or the preview dies with a very unhelpful segmentation fault.  We don't 
			 * set the options but just load them for preview.  Keep in mind that the options file must exist that
			 * we are checking for in check_theme_preview()
			 */
			if ( is_customize_preview() ) {
				$options_not_set = $this->check_theme_preview();
				if ($options_not_set) {  // options aren't set yet so display defaults
					$this->sections = $this->credence_defaults;
					echo '<p>Activate the theme to customize</p>';
				}
			} 

			if (is_customize_preview()) {
                $options_not_set = $this->check_theme_preview();
                if ($options_not_set) {  // options aren't set yet so display defaults
                    //$this->sections = $this->credence_defaults;
            		//print_r($this->sections);
            		echo '<p>The Theme has no options set yet.  Set them in Theme Options once activated.</p>';
            		return;  // don't let the next code run, Redux needs to get this working for preview still
                }
            }
			
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

			// Function to test the compiler hook and demo CSS output.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2); 

			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			
			// Change the default value of a field after it's been set, but before it's been used
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );

			// Dynamically add a section. Can be also used to modify sections/fields
			add_filter('redux/options/'.$this->args['opt_name'].'/sections', array( $this, 'dynamic_section' ) );

		}

	    /**
         * Function to check if this is the first run of live preview
         * while theme is not active and no options set.
         * Hooks into the WP_Customize_Manager class to check if preview and current theme set
        **/
        public function check_theme_preview() {
            global $wpdb;
            if (!get_option($this->args['opt_name'])) { // options not set, need to set them
                if( file_exists( dirname(__FILE__).'/admin/inc/imports/options-main.json' )) {
                    /** @global WP_Filesystem_Direct $wp_filesystem  */
                    global $wp_filesystem;
                    if (empty($wp_filesystem)) {
                        require_once(ABSPATH .'/wp-admin/includes/file.php');
                        WP_Filesystem();
                    }
                    $defaults = $wp_filesystem->get_contents(dirname(__FILE__) .'/admin/inc/imports/options-main.json');
                    $this->credence_defaults = json_decode($defaults, true);
                    //update_option($this->args['opt_name'], $this->credence_defaults);
                    return true;
                }
            }
            return false;
        }

		/**
		 
		 	Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 	Simply include this function in the child themes functions.php file.
		 
		 	NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 	so you must use get_template_directory_uri() if you want to use any of the built in icons
		 
		 **/

		function dynamic_section($sections){
		    //$sections = array();
		    $sections[] = array(
		        'title' => __('Section via hook', 'shcreate'),
		        'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'shcreate'),
				'icon' => 'el-icon-paper-clip',
				    // Leave this as a blank section, no options just some intro text set above.
		        'fields' => array()
		    );
		    return $sections;
		}
		
		
		/**
			Filter hook for filtering the args. 
			Good for child themes to override or add to the args array. Can also be used in other functions.
		**/
		
		function change_arguments($args){
		    //$args['dev_mode'] = true;
		    
		    return $args;
		}
			
		
		/**
			Filter hook for filtering the default value of any given field. Very useful in development mode.
		**/

		function change_defaults($defaults){
		    $defaults['str_replace'] = "Testing filter hook!";
		    
		    return $defaults;
		}


		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {
			
			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists('ReduxFrameworkPlugin') ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_meta_demo_mode_link'), null, 2 );
			}

			// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
			remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );	

		}


		public function setSections() {
			global $wpdb;
			/**
			 	Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 **/


			// Background Patterns Reader
			//$sample_patterns_path = ReduxFramework::$_dir . '../../images/backgrounds/';
			//$sample_patterns_url  = ReduxFramework::$_url . '../../images/backgrounds/';
			//$sample_patterns      = array();

			/*	removing from options since anypage can have a slider

			$slider_select = array();
			$table_name = $wpdb->prefix . 'layerslider';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
				$slider_list = $wpdb->get_results( "SELECT *, name FROM $table_name WHERE flag_deleted=0 and flag_hidden=0" );
				foreach ($slider_list as $k => $v) {
					$slider_select[$v->id] = $v->name;
				}
			}

			$slider_select2 = array();
			$table_name = $wpdb->prefix . 'revslider_sliders';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                $slider_list = $wpdb->get_results( "SELECT * FROM $table_name" );
                foreach ($slider_list as $k => $v) {
                    $slider_select2[$v->id] = $v->alias;
                }
            }
			*/

			// nonce created for importing 
			$import_nonce = wp_create_nonce( 'import_demo_content' ); // used for import

			/*
			if ( is_dir( $sample_patterns_path ) ) :
				
			  if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
			  	$sample_patterns = array();

			    while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

			      if( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
			      	$name = explode(".", $sample_patterns_file);
			      	$name = str_replace('.'.end($name), '', $sample_patterns_file);
			      	$sample_patterns[] = array( 'alt'=>$name,'img' => $sample_patterns_url . $sample_patterns_file );
			      }
			    }
			  endif;
			endif;
			*/

			ob_start();

			$ct = wp_get_theme();
			$this->theme = $ct;
			$item_name = $this->theme->get('Name'); 
			$tags = $this->theme->Tags;
			$screenshot = $this->theme->get_screenshot();
			$class = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf( __( 'Customize &#8220;%s&#8221;','shcreate' ), $this->theme->display('Name') );

			?>
			<div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
				<?php if ( $screenshot ) : ?>
					<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
					<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr( $customize_title ); ?>">
						<img src="<?php echo esc_url( $screenshot ); ?>" alt="<?php esc_attr_e( 'Current theme preview', 'shcreate' ); ?>" />
					</a>
					<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>" alt="<?php esc_attr_e( 'Current theme preview', 'shcreate' ); ?>" />
				<?php endif; ?>

				<h4>
					<?php echo $this->theme->display('Name'); ?>
				</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf( __('By %s','shcreate'), $this->theme->display('Author') ); ?></li>
						<li><?php printf( __('Version %s','shcreate'), $this->theme->display('Version') ); ?></li>
						<li><?php echo '<strong>'.__('Tags', 'shcreate').':</strong> '; ?><?php printf( $this->theme->display('Tags') ); ?></li>
					</ul>
					<p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
					<?php if ( $this->theme->parent() ) {
						printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'shcreate' ) . '</p>',
							__( 'http://codex.wordpress.org/Child_Themes','shcreate' ),
							$this->theme->parent()->display( 'Name' ) );
					} ?>
					
				</div>

			</div>

			<?php
			$item_info = ob_get_contents();
			    
			ob_end_clean();

			$sampleHTML = '';
			if( file_exists( dirname(__FILE__).'/info-html.html' )) {
				/** @global WP_Filesystem_Direct $wp_filesystem  */
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH .'/wp-admin/includes/file.php');
					WP_Filesystem();
				}  		
				$sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__).'/info-html.html');
			}




			// ACTUAL DECLARATION OF SECTIONS

			$this->sections[] = array(
				'icon' => 'el-icon-cogs',
				'title' => __('General Settings', 'shcreate'),
				'fields' => array(
					array(
						'id'=>'layout',
						'type' => 'image_select',
						'title' => __('Main Layout', 'shcreate'), 
						'subtitle' => __('Select between a full width, boxed or boxed offset page layout.  If using a Side Menu Layout, keep this set to full width.', 'shcreate'),
						'options' => array(
								'1' => array('alt' => 'Full', 'img' => get_template_directory_uri() .'/images/layouts/full.png'),
								'2' => array('alt' => 'Boxed', 'img' => get_template_directory_uri() .'/images/layouts/boxed.png'),
								'3' => array('alt' => 'Boxed Offset', 'img' => get_template_directory_uri() 
											.'/images/layouts/boxed-offset.png'),
							),
						'default' => '1'
						),

					array(
						'id' => 'single_page_template',
						'type' => 'switch',
						'title' => __('Enable Single Page Template', 'shcreate'),
						'desc' => __('This enables the javascript for smooth scrolling to different sections of a single page template.  Be sure to turn this on if you would like smooth scrolling to different sections of a page using anchor links.', 'shcreate'),
						'default' => 2,
					),

					array(
                        'id'=>'main-bg-color',
                        'type' => 'color',
                        'title' => __('Page Background Color', 'shcreate'),
                        'subtitle' => __('Default background color is white.', 'shcreate'),
						'desc' => __('This background color is for the actual page background.', 'shcreate'),
                        'default' => '#fff',
                        'validate' => 'color',
                    ),

		            array(
                        'id'=>'favicon',
                        'type' => 'media',
                        'url'=> true,
                        'title' => __('Site Favicon', 'shcreate'),
                        'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
						'subtitle' => __('Upload your site favicon (favicon.ico)', 'shcreate'),
                        'default'=> array('url'=> get_template_directory_uri() .'/images/favicon.ico'),
                        ),

					array(
                        'id'=>'retina-support',
                        'type' => 'switch',
                        'title' => __('Enable Retina Image Support', 'shcreate'),
                        'subtitle'=> __('Allow retina devices to download larger images', 'shcreate'),
                        "default" => 0,
                        ),

					array( 
						'id'=>'chrome-smooth',
						'type' => 'switch',
						'title' => __('Enable Chrome Smooth Scroll', 'shcreate'),
						'subtitle'=>__('Enable Google Chrome browser javascript to perform smooth mouse scrolling.', 'shcreate'),
						'default' => 1,
					),

					array(
                        'id'=>'totop',
                        'type' => 'switch',
                        'title' => __('Enable Return To Top button', 'shcreate'),
                        'subtitle'=>__('Enables convenient To Top button when scrolling down on page.', 'shcreate'),
                        'default' => 1,
                    ),


					array(
						'id'=>'site-fadein',
						'type' => 'switch',
						'title' => __('Enable Site Fade In Effect', 'shcreate'),
						'subtitle' => __('Choose to enable Site fade in on page load or disable for no fade in.', 'shcreate'),
						'default' => 1,
					),

					array(
						'id'=>'linear-icons',
                        'type' => 'switch',
                        'title' => __('Enable Linear Icons', 'shcreate'),
                        'subtitle' => __('Choose to enable Linear Icons.', 'shcreate'),
						'desc' => __('Enabling these font icons will load another font style sheet, you can take a look at these premium fonts at https://linearicons.com/', 'shcreate'),
                        'default' => false,
                    ),
					array(
                        'id'=>'ion-icons',
                        'type' => 'switch',
                        'title' => __('Enable Ion Icons', 'shcreate'),
                        'subtitle' => __('Choose to enable Ion Icons.', 'shcreate'),
                        'desc' => __('Enabling these font icons will load another font style sheet, you can take a look at the fonts at http://ionicons.com/', 'shcreate'),
                        'default' => false,
                    ),


					array(
                        'id'=>'site-font',
                        'type' => 'typography',
                        'title' => __('Site Font', 'shcreate'),
                        'subtitle' => __('Specify the body font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'15px',
							'line-height'=>'21px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                    ),

					array(
						'id' => 'h1-font',
						'type' => 'typography',
                        'title' => __('H1 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 1 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'45px',
                            'line-height'=>'50px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),
					array(
                        'id' => 'h2-font',
                        'type' => 'typography',
                        'title' => __('H2 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 2 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'40px',
                            'line-height'=>'48px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),
					array(
                        'id' => 'h3-font',
                        'type' => 'typography',
                        'title' => __('H3 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 3 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'35px',
                            'line-height'=>'43px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),
					array(
                        'id' => 'h4-font',
                        'type' => 'typography',
                        'title' => __('H4 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 4 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'25px',
                            'line-height'=>'34px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),
					array(
                        'id' => 'h5-font',
                        'type' => 'typography',
                        'title' => __('H5 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 5 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'20px',
                            'line-height'=>'28px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),
					array(
                        'id' => 'h6-font',
                        'type' => 'typography',
                        'title' => __('H6 Font', 'shcreate'),
                        'subtitle' => __('Specify the Header 6 font properties.', 'shcreate'),
                        'google'=>true,
                        'default' => array(
                            'color'=>'#828282',
                            'font-size'=>'18px',
                            'line-height'=>'25px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            ),
                        ),

					array(
						'id'=>'tracking-code',
						'type' => 'textarea',
						'title' => __('Tracking Code', 'shcreate'), 
						'subtitle' => __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.', 'shcreate'),
						//'validate' => 'js'
						),
			        
			        array(
						'id'=>'css-code',
						'type' => 'ace_editor',
						'title' => __('CSS Code', 'shcreate'), 
						'subtitle' => __('Paste your CSS code here.', 'shcreate'),
						'mode' => 'css',
			            'theme' => 'monokai',
					),
			        array(
						'id'=>'js-code',
						'type' => 'ace_editor',
						'title' => __('JS Code', 'shcreate'), 
						'subtitle' => __('Paste any extra JS code here.', 'shcreate'),
						'mode' => 'javascript',
			            'theme' => 'chrome',
					),
				)
			);

			$this->sections[] = array(
				'icon' => 'el-icon-website',
				'title' => __('Styling Options', 'shcreate'),
				'fields' => array(
					// Begin header options
                    array(
                        'id'=>'background-option',
                        'type' => 'radio',
                        'title' => __('Background Option', 'shcreate'),
                        'subtitle' => __('Choose your background (Only applies to boxed layout)', 'shcreate'),
                        'desc' => __('Choices are Color, Repeat Image, Single Image, and None.', 'shcreate'),
                        'options' => array('1' => __('Color', 'shcreate'), '2' => __('Repeat Image', 'shcreate'), 
							'3' => __('Single Image', 'shcreate'), '4' => __('None', 'shcreate') ),
                        'default' => '2',
                        ),


					array(
						'id'=>'color-background',
						'type' => 'color',
						'required'=> array('background-option', 'equals', 1),
						'output' => array('.site-title'),
						'title' => __('Body Background Color', 'shcreate'), 
						'subtitle' => __('Pick a background color for the theme, this applies if you are using the \'boxed page layout\'.', 'shcreate'),
						'default' => '#FFFFFF',
						'validate' => 'color',
						),		

					array(
                        'id'=>'background-pattern',
                        'type' => 'media',
						'required'=> array('background-option', 'equals', 2),
                        'tiles' => true,
						'title' => __('Background tiled image', 'shcreate'),
                        'desc' => __('Set a pattern to be used as your background (for boxed layout). You can get more at http://subtlepatterns.com and upload them to your WordPress Media.', 'shcreate'),
                        'subtitle'=> __('Select a background pattern.', 'shcreate'),
                        'default'       => get_template_directory_uri() . '/images/backgrounds/giftly.png',
                    ),

					array(
                        'id'=>'background-image',
                        'type' => 'media',
						'required' => array('background-option', 'equals', 3),
                        'url'=> true,
                        'title' => __('Single Background Image', 'shcreate'),
                        //'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'subtitle' =>  __('Upload a single background image to be used as the background (for boxed layout).', 'shcreate'),
                        'default'=>array(),
                        ),

					array(
                        'id'=>'color-accent',
                        'type' => 'color',
                        'title' => __('Accent Color', 'shcreate'),
                        'subtitle' => __('Pick the accent color for your theme.', 'shcreate'),
                        'default' => '#e74c3c',
                        'validate' => 'color',
                    ),

					array(
                        'id'=>'link-color',
                        'type' => 'color',
                        'title' => __('General Link Color', 'shcreate'),
                        'subtitle' => __('Choose the general link color.', 'shcreate'),
                        'default' => '#222',
                        'validate' => 'color',
                    ),

				)
			);

			$this->sections[] = array(
				'icon' => 'el-icon-file',
				'title' => __('Header & Side Menus', 'shcreate'),
				'fields' => array (
					// Top Or Side Menu Choice
					array(
						'id'=> 'top-side-menu',
						'type' => 'radio',
						'title' => __('Choose Top Or Side menu placement', 'shcreate'),
						'desc' => __('Choices below will change depending on your selection', 'shcreate'),
						'options' => array('1' => __('Top Menu', 'shcreate'), '2' => __('Side Menu', 'shcreate') ),
						'default' => '1',
					),
					array(
						'id' => 'side-menu-location',
						'type' => 'radio',
						'title' => __('Side Menu Location', 'shcreate'),
						'desc' => __('Choose Left or Right for Side Menu Location', 'shcreate'), 
						'options' => array('1' => __('Left', 'shcreate'), '2' => __('Right', 'shcreate') ),
						'required' => array('top-side-menu', 'equals', 2),
						'default' => '1',
					),
					// Side menu background choices
					array(
                        'id'=>'side-bg-option',
                        'type' => 'radio',
                        'title' => __('Side Menu Background', 'shcreate'),
                        'desc' => __('Choose if you would like to use an image or color for the side menu background.', 'shcreate'),
                        'options' => array('1' => __('Color', 'shcreate'), '2' => __('Image background', 'shcreate') ),
                        'default' => '1',
                        'required' =>  array ('top-side-menu', 'equals', 2),
                    ),

                    array(
                        'id'=>'side-bg-color',
                        'type' => 'color',
                        'required' => array(
                            array ('top-side-menu', 'equals', 2),
                            array ('side-bg-option', 'equals', 1),
                        ),
                        'title' => __('Side Menu Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your side menu.', 'shcreate'),
                        'default' => '#aaaaaa',
                        'validate' => 'color',
                    ),
                    array(
                        'id'=>'side-bg-image',
                        'type' => 'media',
                        'required' => array(
                            array ('top-side-menu', 'equals', 2),
                            array ('side-bg-option', 'equals', 2),
                        ),
                        'tiles' => true,
                        'title' => __('Side Menu Background image', 'shcreate'),
                        'desc' => __('Set an image to be used as your background for the side menu area.', 'shcreate'),
                        'subtitle'=> __('Select a side menu background image.', 'shcreate'),
                        'default'  => get_template_directory_uri() . '/images/backgrounds/knitting250px.png',
                    ),

					array(
                        'id'=>'side-bg-choice',
                        'type' => 'radio',
                        'required' => array(
                            array ('top-side-menu', 'equals', '2'),
                            array ('side-bg-option', 'equals', '2'),
                        ),
                        'title' => __('Choose the Side Menu background image position', 'shcreate'),
                        'desc' => __('Choose if the background should be tiled, fixed, or cover.', 'shcreate'),
                        'options' => array('1' => __('tiled', 'shcreate'), '2' => __('cover', 'shcreate') ),
                        'default' => '1',
                    ),

					array(
                        'id'=>'side-bg-collapsed',
                        'type' => 'color',
                        'required' => array('top-side-menu', 'equals', 2),
                        'title' => __('Side Menu Collapsed Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your side menu for small screens.', 'shcreate'),
                        'default' => '#aaaaaa',
                        'validate' => 'color',
                    ),

					// above navigation menu
					array( 
						'id'=>'above-nav',
                        'type' => 'radio',
                        'title' => __('Enable the Above Bar Section', 'shcreate'),
                        'desc' => __('Choose if you would like to enable the very top banner (above navigation) or not.', 'shcreate'),
                        'options' => array('1' => __('Enable', 'shcreate'), '2' => __('Disable', 'shcreate') ),
                        'default' => '1',
						'required' => array('top-side-menu', 'equals', 1),
                        ),
					// Above navigation Font override
                    array(
                        'id' => 'abovenav-font-over',
                        'type' => 'switch',
                        'title' => __('Override Body Font?', 'shcreate'),
                        'desc' => __('You can choose a different font and font size if you want to use for the above navigation area other than the body font.', 'shcreate'),
                        'default' => false,
                        'required' => array('above-nav', 'equals', 1),
                    ),

                    // Above Nav Font selection
                    array(
                        'id'=>'abovenav-font',
                        'type' => 'typography',
                        'title' => __('Above Navigation Font', 'shcreate'),
                        'subtitle' => __('Specify the above navigation font and font size you would like to use.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'14px',
                            ),
                        'required' => array(
                            array ('above-nav', 'equals', 1),
                            array('abovenav-font-over', 'equals', 1),
                        ),
                    ),
	

					array(
						'id' => 'above-nav-border',
						'type' => 'radio',
						'title' => __('Above Bar Top Border', 'shcreate'),
						'desc' => __('Choose if you want to enable a top border in the above bar (theme accent color).', 'shcreate'),
						'options' => array('1' => __('Enable Top Border', 'shcreate'), 
								'2' => __('Disable Top Border', 'shcreate') 
							),
						'default' => '1',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
							array ('above-nav', 'equals', 1),
						),
					),
					array(
                        'id'=>'header-text',
                        'type' => 'textarea',
						'required' => array(
							array('top-side-menu', 'equals', 1),
							array ('above-nav', 'equals', 1),
						),
                        'title' => __('Above Header Text', 'shcreate'),
                        'subtitle' => __('Enter text, links, etc. for your Above Navigation', 'shcreate'),
                        'default' => 'Call Us Now! 1-800-888-8888 | <a href="mailto:nobody@example.com">nobody@example.com</a>',
                    ),
					array(
                        'id' => 'header-text-side',
                        'type' => 'radio',
                        'title' => __('Above Header Text Side', 'shcreate'),
                        'desc' => __('Choose Left or Right side for Header Text (Social Icons will be on opposite side).', 'shcreate'),
                        'options' => array('1' => __('Left Side', 'shcreate'),
                                '2' => __('Right Side', 'shcreate')
                            ),
                        'default' => '1',
                        'required' => array(
							array ('above-nav', 'equals', 1),
							array ('top-side-menu', 'equals', 1),
						),
                    ),

					/* Custom multi social fields kept in redux extensions */
					array(
                        'id'        => 'opt-multi-social',
                        'type'      => 'multi_social',
                        'title'     => __('Social Icons', 'shcreate'),
                        'subtitle'  => __('Select your site social network links and icons', 'shcreate'),
                        'desc'      => __('Add links in the text field (e.g. http://www.example.com) and icons on the right. Used in above nav or side menu.', 'shcreate'),
                        'custom'    => array(
                            'id' => 'opt-multi-fa',
                            //'data' => 'font-awesome-icons',
							'data' => 'elusive-icons',
                            'type' => 'select'
                        ),
						// 'required' => array('above-nav', 'equals', 1),
                    ),

					array(
                        'id'=>'sidesocial-margin-top',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Social Top Margin', 'shcreate'),
                        'subtitle' => __('Set the social icons top margin', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
						'required' => array('top-side-menu', 'equals', 2),
                    ),
					array(
                        'id'=>'sidesocial-margin-bottom',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Social Bottom Margin', 'shcreate'),
                        'subtitle' => __('Set the social icons bottom margin', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
                        'required' => array('top-side-menu', 'equals', 2),
                    ),

					array(
                        'id'=>'side-menu-text',
                        'type' => 'textarea',
                        'title' => __('Side Menu Text', 'shcreate'),
                        'subtitle' => __('Enter any text for your Side Menu', 'shcreate'),
                        'default' => 'Credence Theme by SH-Themes | Powered by <a href="http://www.wordpress.org">Wordpress</a>
',
                        'required' => array('top-side-menu', 'equals', 2),
                        ),

					 array(
                        'id'=>'side-text-font',
                        'type' => 'typography',
                        'title' => __('Side Menu Text Font', 'shcreate'),
                        'subtitle' => __('If using text in the side menu, you can specify the font for it here seperately from the actual menu font.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'20px',
                            ),
						'required' => array('top-side-menu', 'equals', 2),
                    ),
					array(
                        'id'=>'side-text-margin-top',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Text Top Margin', 'shcreate'),
                        'subtitle' => __('Set the text top margin', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
                        'required' => array('top-side-menu', 'equals', 2),
                    ),
					array(
                        'id'=>'side-text-margin-bottom',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Text Bottom Margin', 'shcreate'),
                        'subtitle' => __('Set the text bottom margin', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
                        'required' => array('top-side-menu', 'equals', 2),
                    ),

					array(
                        'id'=>'above-color',
                        'type' => 'color',
                        'title' => __('Above Text Color', 'shcreate'),
                        'subtitle' => __('Choose the above bar text color.', 'shcreate'),
                        'default' => '#222222',
                        'validate' => 'color',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
							array('above-nav', 'equals', 1),
						),
                    ),
					array(
                        'id'=>'above-bg-color',
                        'type' => 'color',
                        'title' => __('Above Background Color', 'shcreate'),
                        'subtitle' => __('Choose the above bar background color.', 'shcreate'),
                        'default' => '#efefef',
                        'validate' => 'color',
						'required' => array(
							 array ('top-side-menu', 'equals', 1),
							 array ('above-nav', 'equals', 1),
						),
                    ),
					array(
                        'id'=>'above-link-color',
                        'type' => 'color',
                        'title' => __('Above Link Color', 'shcreate'),
                        'subtitle' => __('Choose the link color for the above bar.', 'shcreate'),
                        'default' => '#c4c4c4',
                        'validate' => 'color',
						'required' => array(
							 array ('top-side-menu', 'equals', 1),
							 array ('above-nav', 'equals', 1),
						),
                    ),

					array( 
						'id' => 'menu-search-option',
						'type' => 'radio',
						'title' => __('Menu Search Ability', 'shcreate'),
						'desc' => __('Choose to enable Search from menu or not', 'shcreate'),
						'options' => array('1' => __('Enable Search', 'shcreate'), '2' => __('Disable Search', 'shcreate') ),
						'default' => '1',
						'required' => array ('top-side-menu', 'equals', 1),
					),
					array(
						'id' => 'menu-search-content',
						'type' => 'radio',
						'title' => __('Menu Search Content', 'shcreate'),
						'desc' => __('Choose to search posts only or all content (typically posts only)', 'shcreate'),
						'options' => array('1' => __('Only Posts', 'shcreate'), '2' => __('All Content', 'shcreate') ),
						'default' => '1',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
							array ('menu-search-option', 'equals', 1)
						),
					),
					array (
						'id' => 'menu-language-select',
						'type' => 'radio',
						'title' => __('Menu Language Selector (WPML)', 'shcreate'),
						'desc' => __('Choose to enable a language selector if WPML is active - This one is all you need to fit in with the theme style.', 'shcreate'),
						'options' => array('1' => __('Enable Language Changer', 'shcreate'), '2' => __('Disable Language Changer', 'shcreate') ),
						'default' => '1',
						'required' =>  array ('top-side-menu', 'equals', 1),
					),
					array (
						'id' => 'woo-shopping-cart',
						'type' => 'radio',
						'title' => __('Shopping Cart Menu Item', 'shcreate'),
						'desc' => __('Choose to enable a shopping cart link on your navigation menu (if WooCommerce is installed)', 'shcreate'),
						'options' => array('1' => __('Enable Shopping Cart', 'shcreate'), '2' => __('Disable Shopping Cart', 'shcreate') ),
						'default' => '1',
						'required' =>  array ('top-side-menu', 'equals', 1),
					),
					array(
						'id' => 'nav-option',
						'type' => 'select',
						'title' => __('Choose your top navigation style', 'shcreate'),
						'desc' => __('This sets the top navigation menu style (logo and links)', 'shcreate'),
						'options' => array('1' => __('Left Logo, Right Menu (Classic)', 'shcreate'), 
										'2' => __('Center Logo, Layered', 'shcreate') ,
										'3' => __('Left Logo, Layered', 'shcreate'),
										'4' => __('Right Logo, Layered', 'shcreate'),
										'5' => __('Center Logo, Center Primary', 'shcreate')

									),
						'default' => 1,
						'required' =>  array ('top-side-menu', 'equals', 1),
					),
		
					array(
						'id' => 'top-sticky',
						'type' => 'select',
						'title' => __('Enable Sticky Menu', 'shcreate'),
						'desc' => __('Allow Top Navigation to scroll with page (sticky)', 'shcreate'),
						'options' => array('yes' => __('Yes, Default', 'shcreate'),
										'no' => __('No, Header stays at top', 'shcreate'),
									),
						'default' => 'yes',
					),

					array(
						'id' => 'top-shadow-section',
						'type' => 'select',
						'title' => __('Enable Menu Drop Shadow', 'shcreate'),
						'desc' => __('Add a shadow effect below the header navigation', 'shcreate'),
						'options' => array('no' => __('Disable Shadow', 'shcreate'),
										'yes' => __('Enable Shadow', 'shcreate')
									),
						'default' => 'yes',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
                        	array ('top-sticky', 'equals', 'yes'),
                        ),
					),

					// Top Sticky Animation Control
					array(
						'id' => 'top-sticky-anim',
						'type' => 'select',
						'title' => __('Enable Menu Animation', 'shcreate'),
						'desc' => __('Choose if you would like the menu to animate on scroll', 'shcreate'),
						'options' => array('yes' => __('Yes, Default', 'shcreate'),
										'no' => __('No Animation', 'shcreate'),
									),
						'default' => 'yes',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
							array ('top-sticky', 'equals', 'yes'),
						),
					),

					array(
                        'id'=>'menu-bg-option',
                        'type' => 'radio',
                        'title' => __('Menu Background Option', 'shcreate'),
                        'desc' => __('Choose if you would like to use a tiled image or color for the menu background.', 'shcreate'),
                        'options' => array('1' => __('Color', 'shcreate'), '2' => __('Tiled background', 'shcreate') ),
                        'default' => '1',
						'required' =>  array ('top-side-menu', 'equals', 1),
                    ),
					array(
                        'id'=>'menu-bg-color',
                        'type' => 'color_rgba',
						'required' => array(
							array ('top-side-menu', 'equals', 1),
							array ('menu-bg-option', 'equals', 1),
						),
                        'title' => __('Header Menu Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your header menu.', 'shcreate'),
                        'default' => array (
							'color' => '#ffffff',
							'alpha' => 1
						),
                        'validate' => 'colorrgba',
                    ),
					array(
                        'id'=>'menu-bg-pattern',
                        'type' => 'media',
                        'required' => array(
							array ('top-side-menu', 'equals', 1),
							array ('menu-bg-option', 'equals', 2),
						),
                        'tiles' => true,
                        'title' => __('Header Menu Background tiled image', 'shcreate'),
                        'desc' => __('Set a pattern to be used as your background for the header menu area. You can get more at ht
tp://subtlepatterns.com and upload them to your WordPress media.', 'shcreate'),
                        'subtitle'=> __('Select a menu area tileable background pattern.', 'shcreate'),
                        'default'  => get_template_directory_uri() . '/images/backgrounds/knitting250px.png',
                        ),
					// header logo settings
					array(
                        'id'=>'small-logo',
                        'type' => 'media',
                        'url'=> true,
                        'title' => __('Header Menu Logo', 'shcreate'),
                        'subtitle' =>  __('Upload a logo for your header menu.', 'shcreate'),
                        'default'=>array('url' => get_template_directory_uri() . '/images/small-logo.png'),
                        ),
                    array(
                        'id'=>'logo-height',
                        'type'=>'dimensions',
                        'width'=> 'false',
                        'units_extended' => 'true',
                        'units'    => array('em','px','%'),
                        'title'=> __('Logo Height', 'shcreate'),
                        'subtitle' => __('Set the logo height that goes in the top banner', 'shcreate'),
                        'default' => array('width'=>'1','height'=>'47','units'=>'px'),
                    ),
                    array(
                        'id'=>'logo-width',
                        'type'=>'dimensions',
                        'height'=> 'false',
                        'units_extended' => 'true',
                        'units'    => array('em','px','%'),
                        'title'=> __('Logo Width', 'shcreate'),
                        'subtitle' => __('Set the logo width that goes in the top banner', 'shcreate'),
                        'default' => array('width'=>'230','height'=>'1','units'=>'px'),
                    ),
                    array(
                        'id'=>'logo-margin-top',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Logo Top Margin', 'shcreate'),
                        'subtitle' => __('Set the logo top margin for spacing', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
                    ),
                    array(
                        'id'=>'logo-margin-bottom',
                        'type'=>'dimensions',
                        'width'=>'false',
                        'units_extended' => 'true',
                        'units' => array('px'),
                        'title' => __('Logo Bottom Margin', 'shcreate'),
                        'subtitle' => __('Set the logo bottom margin for spacing', 'shcreate'),
                        'default' => array('height'=>'5','units'=>'px'),
                    ),

					// header menu font style
					array(
                        'id'=>'menu-font',
                        'type' => 'typography',
                        'title' => __('Menu Font', 'shcreate'),
                        'subtitle' => __('Specify the menu font properties. Line height will affect the vertical centering of text inline with the logo for top menus.  So if you want it vertically centered, line height should be the height of your logo + top and bottom logo margins.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
						'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
							'line-height'=>'62px',
                            ),
                    ),

					// header menu font color
					array(
                        'id'=>'menu-font-color',
                        'type' => 'color',
                        'title' => __('Menu Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for your menu.', 'shcreate'),
                        'default' => '#444444',
                        'validate' => 'color',
                    ),

					// Side menu link and active color
					array(
                        'id'=>'side-menu-link',
                        'type' => 'color',
                        'title' => __('Side Menu Link Color', 'shcreate'),
                        'subtitle' => __('Pick the color for side menu links.', 'shcreate'),
                        'default' => '#444444',
                        'validate' => 'color',
                        'required' => array ('top-side-menu', 'equals', '2'),
                    ),
					array(
                        'id'=>'side-menu-hover',
                        'type' => 'color',
                        'title' => __('Side Menu Hover Color', 'shcreate'),
                        'subtitle' => __('Pick the color for side menu link hover.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
                        'required' => array ('top-side-menu', 'equals', '2'),
                    ),

					// Side menu text align and underline menu items
					array (
                        'id'=>'side-menu-align',
                        'type' => 'radio',
                        'required' => array('top-side-menu', 'equals', 2),
                        'title' => __('Side Menu Text & Link Alignment', 'shcreate'),
                        'desc' => __('Choose to center or align links and text to a side', 'shcreate'),
                        'options' => array(
							'1' => __('Centered', 'shcreate'), 
							'2' => __('Left', 'shcreate'), 
							'3' => __('Right', 'shcreate') ),
                        'default' => '1',
                    ),
					array (
                        'id'=>'side-menu-underline',
                        'type' => 'radio',
                        'required' => array('top-side-menu', 'equals', 2),
                        'title' => __('Menu Item Underlines', 'shcreate'),
                        'desc' => __('Choose if you would like menu links to be underlined', 'shcreate'),
                        'options' => array('1' => __('No Underline', 'shcreate'), '2' => __('Underline', 'shcreate')),
                        'default' => '1',
                    ),

					// Dropdown font color
					array(
                        'id'=>'dropdown-font-color',
                        'type' => 'color',
                        'title' => __('Dropdown Menu Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for your header menu dropdown.', 'shcreate'),
                        'default' => '#444444',
                        'validate' => 'color',
						'required' => array ('top-side-menu', 'equals', '1'),
                    ),
					// Dropdown background color
					array(
                        'id'=>'dropdown-bg-color',
                        'type' => 'color_rgba',
                        'title' => __('Dropdown Menu Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your header menu dropdown.', 'shcreate'),
						'default' => array (
                            'color' => '#ffffff',
                            'alpha' => 1
                        ),
                        'validate' => 'colorrgba',
						'required' => array ('top-side-menu', 'equals', '1'),
                    ),
					// Dropdown highlight color
					array(
                        'id'=>'dropdown-highlight-color',
                        'type' => 'color',
                        'title' => __('Dropdown Menu Highlight Color', 'shcreate'),
                        'subtitle' => __('Pick the highlight color for active menu items.', 'shcreate'),
                        'default' => '#eee',
                        'validate' => 'color',
						'required' => array ('top-side-menu', 'equals', '1'),
                    ),

					// Breadcrumbs
					array(
					'id'=>'breadcrumb-enable',
                        'type' => 'radio',
                        'title' => __('Enable breadcrumbs', 'shcreate'),
                        'desc' => __('Choose if you would like to enable the breadcrumb bar.', 'shcreate'),
                        'options' => array('1' => __('Enable', 'shcreate'), '2' => __('Disable', 'shcreate') ),
                        'default' => '1',
                    ),
					array(
                        'id'=>'breadcrumb-option',
                        'type' => 'radio',
                        'title' => __('Enable Breadcrumb Background Image', 'shcreate'),
                        'desc' => __('Choose if you would like to use a background image for the breadcrumb header section.', 'shcreate'),
                        'options' => array('1' => __('Yes', 'shcreate'), '2' => __('No', 'shcreate') ),
                        'default' => '1',
						'required' => array(
                            array ('breadcrumb-enable', 'equals', '1'),
                    	),
                    ),
					array(
                        'id'=>'breadcrumb-bg',
                        'type' => 'media',
						'required' => array(
							array ('breadcrumb-enable', 'equals', '1'),
							array ('breadcrumb-option', 'equals', '1'),
						),
                        'url'=> true,
                        'title' => __('Breadcrumb Section Background', 'shcreate'),
                        'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'subtitle' => __('Upload a background for the breadcrumb section', 'shcreate'),
                        'default'=>array('url'=> get_template_directory_uri() .'/images/backgrounds/absurdidad.png'),
                    ),
					array(
                        'id'=>'breadcrumb-bg-option',
                        'type' => 'radio',
						'required' => array(
							array ('breadcrumb-enable', 'equals', '1'),
							array ('breadcrumb-option', 'equals', '1'),
						),
                        'title' => __('Choose the background image position', 'shcreate'),
                        'desc' => __('Choose if the background should be tiled, fixed, or cover.', 'shcreate'),
                        'options' => array('1' => __('tiled', 'shcreate'), '2' => __('fixed', 'shcreate'), 
							'3' => __('cover', 'shcreate') ),
                        'default' => '1',
                    ),
					// Breadcrumb background color
					array(
                        'id'=>'breadcrumb-bg-color',
                        'type' => 'color',
                        'title' => __('Breadcrumb Area Background Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for the breadcrumb area.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
						'required' => array(
                            array ('breadcrumb-enable', 'equals', '1'),
                            array ('breadcrumb-option', 'equals', '2'),
                        ),
                    ),
					//  Breadcrumb font color
                    array(
                        'id'=>'breadcrumb-font-color',
                        'type' => 'color',
                        'title' => __('Breadcrumb Area Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for the breadcrumb area.', 'shcreate'),
                        'default' => '#555',
                        'validate' => 'color',
						'required' => array(
                            array ('breadcrumb-enable', 'equals', '1'),
                        ),
                    ),
					//  Breadcrumb font color
                    array(
                        'id'=>'breadcrumb-link-color',
                        'type' => 'color',
                        'title' => __('Breadcrumb Area Link Color', 'shcreate'),
                        'subtitle' => __('Pick the link color for the breadcrumb area.', 'shcreate'),
                        'default' => '#555',
                        'validate' => 'color',
						'required' => array(
                            array ('breadcrumb-enable', 'equals', '1'),
                        ),
                    ),

					// Enable Top Slide Down
					array(
						'id' => 'top-slider',
						'type' => 'radio',
						'title' => __('Enable Top Slidedown Widget Area', 'shcreate'),
						'subtitle' => __('Enable a Top Widget area that slides down and can hold widgets', 'shcreate'),
						'options' => array('1' => __('Enable', 'shcreate'), '2' => __('Disable', 'shcreate') ),
						'default' => '1',
						'required' => array ('top-side-menu', 'equals', '1'),
					),

					// Slidedown Font override
                    array(
                        'id' => 'slidedown-font-over',
                        'type' => 'switch',
                        'title' => __('Override Body Font?', 'shcreate'),
                        'desc' => __('You can choose a different font and font size if you want to use for the Top Slidedown area
 other than the body font.', 'shcreate'),
                        'default' => false,
                        'required' => array('top-slider', 'equals', 1),
                    ),

                    // Slidedown Font selection
                    array(
                        'id'=>'slidedown-font',
                        'type' => 'typography',
                        'title' => __('Top Slidedown Font', 'shcreate'),
                        'subtitle' => __('Specify the Top Slidedown font and font size you would like to use.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'14px',
                            ),
                        'required' => array(
                            array ('top-slider', 'equals', 1),
                            array('slidedown-font-over', 'equals', 1),
                        ),
                    ),


					// Top Slide Background color
					array(
                        'id'=>'top-slider-bg',
                        'type' => 'color',
                        'title' => __('Top Slider Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for the Top Slider area.', 'shcreate'),
                        'default' => '#555',
                        'validate' => 'color',
						'required' => array(
							array ('top-side-menu', 'equals', '1'),
							array('top-slider', 'equals', 1),
						),
                    ),
					// Top Slide Font Color
                    array(
                        'id'=>'top-slider-font',
                        'type' => 'color',
                        'title' => __('Top Slider Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for the Top Slider area.', 'shcreate'),
                        'default' => '#eeeeee',
                        'validate' => 'color',
                        'required' => array(
							array ('top-side-menu', 'equals', '1'),
							array('top-slider', 'equals', 1),
						),
                    ),
					// Top Slide Link color
                    array(
                        'id'=>'top-slider-link',
                        'type' => 'color',
                        'title' => __('Top Slider Link Color', 'shcreate'),
                        'subtitle' => __('Pick the link color for the Top Slider area.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
						'required' => array(
							array ('top-side-menu', 'equals', '1'),
							array ('top-slider', 'equals', 1),
						),
                    ),
					// Top Slide Header color
                    array(
                        'id'=>'top-slider-header',
                        'type' => 'color',
                        'title' => __('Top Slider Header Color', 'shcreate'),
                        'subtitle' => __('Pick the text color for Top Slider Headers (h1, h2, etc).', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
                        'required' => array(
							array ('top-side-menu', 'equals', '1'),
							array('top-slider', 'equals', 1),
						),
                    ),
					// Top Slide Column Number
					array(
						'id'=>'top-slider-columns',
                        'type' => 'select',
                        'title'     => __('Number of Columns', 'shcreate'),
                        'desc'      => __('Set the number of columns from 1 to 4 for the top area content', 'shcreate'),
                        'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4'),
                        'default' => '3',
						'required' => array(
							array('top-side-menu', 'equals', '1'),
							array('top-slider', 'equals', 1),
						),
					),
				)
			);



			$this->sections[] = array(
                'icon' => 'el-icon-file',
                'title' => __('Footer Section', 'shcreate'),
                'fields' => array (
					array( 
						'id' => 'footerw-enable',
						'type' => 'radio', 
						'title' => __('Enable Footer Widget Area?', 'shcreate'),
						'desc' => __('If you enable this area you will have options to customize it.  After enabling, go to Appearance -> Widgets and add content to Footer', 'shcreate'),
						'options' => array('1' => __('Enable', 'shcreate'), '2' => __('Disable', 'shcreate') ),
						'default' => '1',

					),
					// FooterW Body font override
					array(
                        'id' => 'footerw-font-over',
                        'type' => 'switch',
                        'title' => __('Override Body Font?', 'shcreate'),
                        'desc' => __('You can choose a different font and font size to use for the footer widget area other than the body font.', 'shcreate'),
                        'default' => false,
						'required' => array('footerw-enable', 'equals', 1),
                    ),

					// Footer Widget Font
                    array(
                        'id'=>'footerw-font',
                        'type' => 'typography',
                        'title' => __('Footer Widget Font', 'shcreate'),
                        'subtitle' => __('Specify the footer widget area font and font size you would like to use.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'14px',
                            ),
                        'required' => array(
                            array ('footerw-enable', 'equals', 1),
                            array('footerw-font-over', 'equals', 1),
                        ),
                    ),


					// Footer Widget Column Number
                    array(
                        'id'=>'footerw-columns',
                        'type' => 'select',
                        'title'     => __('Number of Columns', 'shcreate'),
                        'desc'      => __('Set the number of columns you are using from 1 to 4 for the footer widget area. This should match the number of widgets you plan to use.', 'shcreate'),
                        'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4'),
                        'default' => '4',
                        'required' => array('footerw-enable', 'equals', 1),
                    ),
	                array(
                        'id'=>'footerw-bg-option',
                        'type' => 'radio',
                        'title' => __('Footer Widget Area Background Option', 'shcreate'),
                        'desc' => __('Choose if you would like to use a background color, or tiled image for the footer widget area.', 'shcreate'),
                        'options' => array('1' => __('None', 'shcreate'), '2' => __('Color', 'shcreate'), 
							'3' => __('Tiled Background', 'shcreate'),
							'4' => __('Single Image', 'shcreate') ),
                        'default' => '2',
						'required' => array('footerw-enable', 'equals', 1),
                     ),
					 array(
                        'id'=>'footerw-bg-color',
                        'type' => 'color',
                        'required' => array(
							array ('footerw-bg-option', 'equals', 2),
							array('footerw-enable', 'equals', 1),
						),
                        'title' => __('Footer Widget Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your footer widget area.', 'shcreate'),
                        'default' => '#555555',
                        'validate' => 'color',
                    ),
                    array(
                        'id'=>'footerw-bg-pattern',
                        'type' => 'media',
                        'required' => array(
							array('footerw-bg-option', 'equals', 3),
							array('footerw-enable', 'equals', 1),
						),
                        'tiles' => true,
                        'title' => __('Footer Widget Area background tiled image', 'shcreate'),
                        'desc' => __('Set a pattern to be used as your background for the footer widget area. You can get more at ht
tp://subtlepatterns.com and upload them to your WordPress media.', 'shcreate'),
                        'subtitle'=> __('Select a footer area background pattern.', 'shcreate'),
                        'default'  => get_template_directory_uri() . '/images/backgrounds/knitting250px.png',
                    ),
					array(
						'id'=> 'footerw-bg-image',
						'type' => 'media',
						'required' => array(
							array('footerw-bg-option', 'equals', 4),
							array('footerw-enable', 'equals', 1),
						),
						'title' => __('Select an Image to use as your background.', 'shcreate'),
						'subtitle' => __('Select a footer widget area background image.', 'shcreate'),
					),

					 //  footer widget font color
                    array(
                        'id'=>'footerw-font-color',
                        'type' => 'color',
                        'title' => __('Footer Widget Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for your footer widget area.', 'shcreate'),
                        'default' => '#efefef',
                        'validate' => 'color',
						'required' => array('footerw-enable', 'equals', 1),
                    ),
					// Footer Widget Header color
                    array(
                        'id'=>'footerw-header-color',
                        'type' => 'color',
                        'title' => __('Footer Widget Header Color', 'shcreate'),
                        'subtitle' => __('Pick the text color for Footer Widget Headers (h1, h2, etc).', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
						'required' => array('footerw-enable', 'equals', 1),
                    ),

					// Footer below footer widget
					array(
						'id' => 'footer-enable',
						'type' => 'radio',
						'title' => __('Enable the bottom Footer', 'shcreate'),
						'desc' => __('Choose if you would like to disable the bottom footer', 'shcreate'),
						'options' => array('1' => __('Enable Bottom Footer', 'shcreate'), 
								'2' => __('Disable Bottom Footer', 'shcreate') ),
						'default' => '1',
					),
				
					// Footer Body font override
                    array(
                        'id' => 'footer-font-over',
                        'type' => 'switch',
                        'title' => __('Override Body Font?', 'shcreate'),
                        'desc' => __('You can choose a different font and font size to use for the bottom footer area other than the
 body font.', 'shcreate'),
                        'default' => false,
                        'required' => array('footer-enable', 'equals', 1),
                    ),

                    // Footer Font
                    array(
                        'id'=>'footer-font',
                        'type' => 'typography',
                        'title' => __('Bottom Footer Font', 'shcreate'),
                        'subtitle' => __('Specify the footer widget area font and font size you would like to use.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'14px',
                            ),
                        'required' => array(
                            array ('footerw-enable', 'equals', 1),
                            array('footer-font-over', 'equals', 1),
                        ),
                    ),

		            array(
                        'id'=>'footer-text',
                        'type' => 'textarea',
                        'title' => __('Footer Text', 'shcreate'),
                        'subtitle' => __('Enter the text for your footer', 'shcreate'),
                        'default' => 'Credence Theme 2014 by SH-Themes | Powered by <a href="http://www.wordpress.org">Wordpress</a>',
						'required' => array('footer-enable', 'equals', 1),
                        ),
					 array(
                        'id'=>'footer-bg-option',
                        'type' => 'radio',
                        'title' => __('Footer Background Option', 'shcreate'),
                        'desc' => __('Choose if you would like to use a tiled image or color for the footer background.', 'shcreate'),
                        'options' => array('1' => __('Color', 'shcreate'), '2' => __('Tiled background', 'shcreate') ),
                        'default' => '1',
						'required' => array('footer-enable', 'equals', 1),
                        ),
                    array(
                        'id'=>'footer-bg-color',
                        'type' => 'color',
                        'required' => array('footer-bg-option', 'equals', 1),
                        'title' => __('Footer Menu Background Color', 'shcreate'),
                        'subtitle' => __('Pick the background color for your footer.', 'shcreate'),
                        'default' => '#444444',
                        'validate' => 'color',
						'required' => array(
							array ('footer-enable', 'equals', 1),
							array ('footer-bg-option', 'equals', 1),
						),
                    ),
                    array(
                        'id'=>'footer-bg-pattern',
                        'type' => 'media',
                        'required' => array('footer-bg-option', 'equals', 2),
                        'tiles' => true,
                        'title' => __('Footer Background tiled image', 'shcreate'),
                        'desc' => __('Set a pattern to be used as your background for the footer area. You can get more at http://subtlepatterns.com and upload them to your WordPress media.', 'shcreate'),
                        'subtitle'=> __('Select a footer background pattern.', 'shcreate'),
						'required' => array(
							array ('footer-enable', 'equals', 1),
							array ('footer-bg-option', 'equals', 2),
                        ),
					),
                    //  footer font color
                    array(
                        'id'=>'footer-font-color',
                        'type' => 'color',
                        'title' => __('Footer Font Color', 'shcreate'),
                        'subtitle' => __('Pick the font color for your footer.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
						'required' => array('footer-enable', 'equals', 1),
                    ),
					array(
                        'id'=>'footer-override',
                        'type' => 'radio',
                        'title' => __('Footer Override Accent Color', 'shcreate'),
                        'desc' => __('Override Theme Accent colors for links in footer.', 'shcreate'),
                        'options' => array('1' => __('No', 'shcreate'), '2' => __('Yes', 'shcreate') ),
                        'default' => '1',
                        'required' => array('footer-enable', 'equals', 1),
                    ),
					// Footer Link Overrides
					array(
                        'id'=>'footer-link-color',
                        'type' => 'color',
                        'title' => __('Footer Link Color', 'shcreate'),
                        'subtitle' => __('Pick the color for your footer links.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
                        'required' => array(
							array('footer-enable', 'equals', 1),
							array('footer-override', 'equals', 2),
						),
                    ),
					array(
                        'id'=>'footer-hover-color',
                        'type' => 'color',
                        'title' => __('Footer Hover Color', 'shcreate'),
                        'subtitle' => __('Pick the color for your footer link hover.', 'shcreate'),
                        'default' => '#ffffff',
                        'validate' => 'color',
						'required' => array(
                            array('footer-enable', 'equals', 1),
                            array('footer-override', 'equals', 2),
                        ),
                    ),
					array(
                        'id'=>'footer-center-text',
                        'type' => 'radio',
                        'title' => __('Center Footer Text', 'shcreate'),
                        'desc' => __('Use to center your footer text if you aren\'t planning on having a footer menu.', 'shcreate'),
                        'options' => array('1' => __('No', 'shcreate'), '2' => __('Yes', 'shcreate') ),
                        'default' => '1',
                        'required' => array('footer-enable', 'equals', 1),
                    ),
                )
            );


			$theme_info = '<div class="redux-framework-section-desc">';
			$theme_info .= '<p class="redux-framework-theme-data description theme-uri">'.__('<strong>Theme URL:</strong> ', 'shcreate').'<a href="'.$this->theme->get('ThemeURI').'" target="_blank">'.$this->theme->get('ThemeURI').'</a></p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-author">'.__('<strong>Author:</strong> ', 'shcreate').$this->theme->get('Author').'</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-version">'.__('<strong>Version:</strong> ', 'shcreate').$this->theme->get('Version').'</p>';
			$theme_info .= '<p class="redux-framework-theme-data description theme-description">'.$this->theme->get('Description').'</p>';
			$tabs = $this->theme->get('Tags');
			if ( !empty( $tabs ) ) {
				$theme_info .= '<p class="redux-framework-theme-data description theme-tags">'.__('<strong>Tags:</strong> ', 'shcreate').implode(', ', $tabs ).'</p>';	
			}
			$theme_info .= '</div>';

			if(file_exists(dirname(__FILE__).'/README.md')){
			$this->sections['theme_docs'] = array(
						'icon' => ReduxFramework::$_url.'assets/img/glyphicons/glyphicons_071_book.png',
						'title' => __('Documentation', 'shcreate'),
						'fields' => array(
							array(
								'id'=>'17',
								'type' => 'raw',
								'content' => file_get_contents(dirname(__FILE__).'/README.md')
								),				
						),
						
						);
			}//if

			// Blog Settings
			$this->sections[] = array(
				'icon' => ' el-icon-star-alt',
				'title' => __('Blog Settings', 'shcreate'),
				'desc'  => __('<p class="description">Settings for the Blog</p>', 'shcreate'),
				'fields' => array(

					array(
                        'id'        => 'blog-attributes',
                        'type'      => 'select',
                        'multi'     => true,
                        'title'     => __('Blog Attributes', 'shcreate'),
                        'subtitle'  => __('Choose the attributes you would like to appear on your blog entries, and put them in the order you would like.', 'shcreate'),

                        //Must provide key => value pairs for radio options
                        'options'   => array(
                            '1' => __('Posted By', 'shcreate'),
                            '2' => __('Date', 'shcreate'),
                            '3' => __('Category', 'shcreate'),
							'4' => __('Tags', 'shcreate'),
							'5' => __('Comments', 'shcreate'),
							'6' => __('Likes', 'shcreate'),
                        ),
                        'default'   => array('1', '2', '3', '4', '5', '6'),
                    ),
					array(
                        'id'=>'blog-right-left',
                        'type' => 'radio',
                        'title' => __('Sidebar for other pages', 'shcreate'),
                        'desc' => __('Choose left or right for the sidebar based on the blog template you have chosen.  This affects other pages like archives and categories etc..  Remember to select a blog template for your main blog page as well.', 'shcreate'),
                        'options' => array('1' => __('Right Side', 'shcreate'), 
								'2' => __('Left Side', 'shcreate'), 
								'3' => __('None', 'shcreate') ),
                        'default' => '1',
                    ),
					array(
						'id' => 'blog-layout',
						'type'  => 'radio',
						'title' => __('Choose Layout', 'shcreate'),
						'desc' => __('Choose large (top) images, or medium (left) images for your blog', 'shcreate'),
						'options' => array('1' => __('Large', 'shcreate'), '2' => __('Medium', 'shcreate')),
						'default' => '1',
					),
					array(
                        'id' => 'blog-summary-full',
                        'type'  => 'radio',
                        'title' => __('Display Summary or Full Text', 'shcreate'),
                        'desc' => __('Note: this only applies to the standard blog layouts (large or medium)', 'shcreate'),
                        'options' => array('1' => __('Summary', 'shcreate'), '2' => __('Full', 'shcreate')),
                        'default' => '1',
                    ),

					 // Sidebar Font override
                    array(
                        'id' => 'sidebar-font-over',
                        'type' => 'switch',
                        'title' => __('Override Body Font?', 'shcreate'),
                        'desc' => __('You can choose a different font and font size if you want to use for Sidebar text other than the body font.', 'shcreate'),
                        'default' => false,
                    ),

                    // Sidebar Font selection
                    array(
                        'id'=>'sidebar-font',
                        'type' => 'typography',
                        'title' => __('Sidebar Font', 'shcreate'),
                        'subtitle' => __('Specify the Sidebar font and font size you would like to use.', 'shcreate'),
                        'google'=>true,
                        'color'=>false,
                        'text-align'=>false,
                        'default' => array(
                            'font-size'=>'14px',
                            'font-family'=>'Roboto Condensed',
                            'font-weight'=>'400',
                            'line-height'=>'14px',
                        ),
						'required' => array('sidebar-font-over', 'equals', 1),
                    ),

				),
			);

			// Woo Commerce settings
            $this->sections[] = array(
                'icon' => ' el-icon-shopping-cart-sign',
                'title' => __('Woo Commerce', 'shcreate'),
                'desc'  => __('<p class="description">If you are going to use WooCommerce with the theme, these are some extra settings you can set up for the shopping section.</p>', 'shcreate'),
                'fields' => array(
                    array(
                        'id'        => 'woo-sidebar',
                        'type'      => 'radio',
                        'title'     => __('Shop Sidebar', 'shcreate'),
                        'subtitle'  => __('Choose if you are using the sidebar and the side to use it on.', 'shcreate'),
						'options' => array('1' => __('Right Sidebar', 'shcreate'), '2' => __('Left Sidebar', 'shcreate'), '3' => __('None', 'shcreate') ),
						'default' => '1'
					),
					array(
						'id' 		=> 'woo-transition',
						'type' 		=> 'radio',
						'title' 	=> __('Allow Items to fade in', 'shcreate'),
						'subtitle'  => __('You can have your shop items animate in if this option is enabled', 'shcreate'),
						'options' => array('1' => __('Enable', 'shcreate'), '2' => __('Disable', 'shcreate') ),
						'default' => '1'
					),
				),
			);
						

			$this->sections[] = array(
				'icon' => 'el-icon-info-circle',
				'title' => __('Theme Information', 'shcreate'),
				'fields' => array(
					array(
						'id'=>'raw_new_info',
						'type' => 'raw',
						'content' => $item_info,
						)
					),   
				);
	
			$this->sections[] = array(
                'type' => 'divide',
            );

			$this->sections[] = array(
				'icon' => 'el-icon-wrench-alt',
				'title' => __('Theme Updates', 'shcreate'),
				'fields' => array(
					array(
					'id' => 'info_normal',
					'type' => 'info',
					'desc' => __('<h4>Theme Update Settings</h4> Starting with version 2.0.0 (and up) Automatic theme updates are available without entering your purchase information anymore.  You will receive automatic update notifications when updates are available in your WordPress Dashboard.', 'shcreate'),
					//'desc' => __('<h4>Theme Update Settings</h4> Select the marketplace you purchased the theme from and enter the details to receive automatic theme updates.  If you bought it from another marketplace, send an email to info@sh-themes.com with your purchase details and we will send you a key to receive automatic updates upon confirmation.', 'shcreate'),
					),
					/*
					array(
						'id'=>'update-validation',
						'type' => 'select',
                        'title'     => __('Theme Purchased From', 'shcreate'),
                        'desc'      => __('Select where you purchased your theme, choose custom if the site is not listed.', 'shcreate'),
						'options' => array('mojo' => 'Mojo Marketplace', 'themeforest' => 'Theme Forest', 'custom' => 'Custom - Other'),
                        'default' => 'mojo'
					),

					array(
						'id'=> 'update-mojo-code',
						'type' => 'text',
						'title' => __('Mojo Marketplace Purchase Code', 'shcreate'),
						'desc' => __('Enter your Mojo Marketplace Purchase Code for the theme.', 'shcreate'),
						'required' => array('update-validation', 'equals', 'mojo'),
					),

					array(
                        'id'=> 'update-themeforest-code',
                        'type' => 'text',
                        'title' => __('Theme Forest Purchase Code', 'shcreate'),
                        'desc' => __('Enter your Theme Forest Purchase Code (Can be found under downloads).', 'shcreate'),
                        'required' => array('update-validation', 'equals', 'themeforest'),
                    ),

					array(
						'id' => 'update-custom-code',
						'type' => 'text',
						'title' => __('Custom Access', 'shcreate'),
						'desc' => __('Enter the code provided from sh-themes after you have sent an email request for validation from another marketplace (e.g. Mojo Themes)', 'shcreate'),
						'required' => array('update-validation', 'equals', 'custom'),
					),
					*/
				),
			);	


			$this->sections[] = array(
                'icon' => 'el-icon-universal-access',
                'title' => __('API Settings', 'shcreate'),
                'fields' => array(

					// Twitter Settings
					array(
    					'id'   => 'info_normal',
    					'type' => 'info',
    					'desc' => __('<h4>Twitter API Settings</h4>Below are settings you need if you plan to use the Twitter widgets and shortcodes in the theme.  Twitter updated their access API and now require you to provide the following information.  To obtain this information, please log into Twitter at <a href="https://apps.twitter.com/" target="blank">https://apps.twitter.com/</a> and follow the directions to "Create New App"', 'shcreate')
					),
					array(
                        'id'=>'twit-consumerkey',
                        'type' => 'text',
                        'title' => __('API Key / Consumer Key:', 'shcreate'),
                        'default' => 'Your Twitter Consumer Key'
                    ),
					array(
                        'id'=>'twit-consumersecret',
                        'type' => 'text',
                        'title' => __('API Secret / Consumer Secret:', 'shcreate'),
                        'default' => 'Your Twitter Consumer Secret'
                    ),
					array(
                        'id'=>'twit-accesstoken',
                        'type' => 'text',
                        'title' => __('Access Token:', 'shcreate'),
                        'default' => 'Your Twitter Access Token'
                    ),
					array(
                        'id'=>'twit-accesstokensecret',
                        'type' => 'text',
                        'title' => __('Access Token Secret:', 'shcreate'),
                        'default' => 'Your Twitter Access Token Secret'
                    ),
					// Gmap api key
					array(
						'id' => 'info_gmap',
						'type' => 'info',
						'desc' => __('<h4>Google Maps API Key</h4>Set your Google Map API Key if you want to use the Google Map Shortcode or Visual composer element.  You can learn how to obtain your free key at ', 'shcreate') . 'https://developers.google.com/maps/documentation/javascript/get-api-key#key',
					),
					array(
						'id'=> 'google-map-key',
						'type' => 'text',
						'title' => __('Google Map API Key', 'shcreate'),
						'default' => ''
					),
				),
            );

			// Include the style switcher capability for the demos
			if ($this->_current_user->user_login == 'scripthat'
				|| $this->_current_user->user_login == 'sh-themes'
			) {
				$this->sections[] = array(
					'icon'  => 'el-icon-wrench',
					'title' => __('Enable Style Switcher', 'shcreate'),
					'heading' => __('This is just for the demo to enable the style switcher capability.', 'shcreate'),
					'fields'  => array(
					 	array(
                        	'id'=>'style-switcher',
                        	'type' => 'switch',
                        	'title' => __('Demo mode style switcher', 'shcreate'),
                        	'subtitle'=> __('Enable the Style switcher', 'shcreate'),
                        	"default"       => 2,
                        ),
					)
				);
			}


			if(file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
			    $tabs['docs'] = array(
					'icon' => 'el-icon-book',
					    'title' => __('Documentation', 'shcreate'),
			        'content' => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
			    );
			}

		}	

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
			    'id' => 'redux-opts-1',
			    'title' => __('Theme Information 1', 'shcreate'),
			    'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'shcreate')
			);

			$this->args['help_tabs'][] = array(
			    'id' => 'redux-opts-2',
			    'title' => __('Theme Information 2', 'shcreate'),
			    'content' => __('<p>This is the tab content, HTML is allowed.</p>', 'shcreate')
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'shcreate');

		}


		/**
			
			All the possible arguments for Redux.
			For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

		 **/
		public function setArguments() {
			
			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
	            
	            // TYPICAL -> Change these values as you need/desire
				'opt_name'          	=> 'shcreate', // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'			=> $theme->get('Name'), // Name that appears at the top of your panel
				'display_version'		=> $theme->get('Version'), // Version that appears at the top of your panel
				'menu_type'          	=> 'menu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'     	=> true, // Show the sections below the admin menu item or not
				'menu_title'			=> __( 'Theme Options', 'shcreate' ),
	            'page'		 	 		=> __( 'Sample Options', 'shcreate' ),
	            'google_api_key'   	 	=> 'AIzaSyBdBi9RS6Owbj8G0V_DfuCJWGMtW5D1JlU', // Must be defined to add google fonts to the typography module
	            'global_variable'    	=> '', // Set a different name for your global variable other than the opt_name
	            'dev_mode'           	=> false, // Show the time the page took to load, etc

	            // OPTIONAL -> Give you extra features
	            'page_priority'      	=> null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	            'page_parent'        	=> 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	            'page_permissions'   	=> 'manage_options', // Permissions needed to access the options panel.
	            'menu_icon'          	=> '', // Specify a custom URL to an icon
	            'last_tab'           	=> '', // Force your panel to always open to a specific tab (by id)
	            'page_icon'          	=> 'icon-themes', // Icon displayed in the admin panel next to your menu_title
	            'page_slug'          	=> '_options', // Page slug used to denote the panel
	            'save_defaults'      	=> true, // On load save the defaults to DB before user clicks save or not
	            'default_show'       	=> false, // If true, shows the default value next to each field that is not the default value.
	            'default_mark'       	=> '', // What to print by the field's title if the value shown is default. Suggested: *


	            // CAREFUL -> These options are for advanced use only
	            'transient_time' 	 	=> 60 * MINUTE_IN_SECONDS,
	            'output'            	=> true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	            'output_tab'            => true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	            //'domain'             	=> 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
	            'footer_credit'      	=> ' ', // Disable the footer credit of Redux. Please leave if you can help it.
	            

	            // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	            'customizer'         	=> false, // Enable customizer support
	            'database'           	=> '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	            
	        
	            'show_import_export' 	=> true, // REMOVE
	            'system_info'        	=> false, // REMOVE
	            
	            'help_tabs'          	=> array(),
	            'help_sidebar'       	=> '', // __( '', $this->args['domain'] );            
				'disable_tracking'      => true // supposed to remove tracking from class ReduxCore/inc/tracking.php
				);
	 
			// Panel Intro text -> before the form
			if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false ) {
				if (!empty($this->args['global_variable'])) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace("-", "_", $this->args['opt_name']);
				}
			} else {
				$this->args['intro_text'] = __('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'shcreate');
			}

			// Add content after the form.
			$this->args['footer_text'] = '<p>' . $this->args['display_name'] . ' ' . $this->args['display_version'] . '</p>';
		}

	}
