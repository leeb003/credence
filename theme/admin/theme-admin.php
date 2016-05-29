<?php
/*
   Admin Main Class
*/

class themeAdmin {
	//properties
	public $config = array();

	//methods

    public function __construct() {
		if ( function_exists( 'add_theme_support')){
    		add_theme_support( 'post-thumbnails' );
		}
		add_image_size( 'admin-list-thumb', 80, 80, true); //admin thumbnail

		/* 
         * Theme Support for custom header and custom background - todo build out functions to tie into theme options
         */
        //add_action('after_setup_theme', array($this, 'theme_support_items') );

		/*
        * Switches default core markup for search form, comment form,
        * and comments to output valid HTML5.
        */
        add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

		/*
		 * Theme Support for title tag
		 */
		add_theme_support( 'title-tag' );

        /*
        * Theme supported post types
        * See http://codex.wordpress.org/Post_Formats
        */
        add_theme_support( 'post-formats', array(
            'audio','video', 'gallery'
        ) );

		add_theme_support("automatic-feed-links");

		// Retina images creation and deletion filters (if enabled in theme options...they might want a plugin to manage)
		global $shcreate;
		if ($shcreate['retina-support'] == 1) {
			add_filter( 'wp_generate_attachment_metadata', array(&$this, 'retina_support_attachment_meta'), 10, 2 );
		}
		// It's safe to leave this enabled since it deletes the @2x on main image removal...I think
		add_filter( 'delete_attachment', array(&$this, 'delete_retina_support_images') );

		// enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );

		// import demo action
		add_action("wp_ajax_import_demo_content", array(&$this, "import_demo_content") );

		// Updates through our own server
		require dirname( __FILE__ ) . '/theme-updates/theme-update-checker.php';
		$MyThemeUpdateChecker = new ThemeUpdateChecker(
			'credence', //Theme slug. Usually the same as the name of its directory.
			'http://updates.sh-themes.com/server/?action=get_metadata&slug=credence' //Metadata URL.
		);
		$MyThemeUpdateChecker->addQueryArgFilter('credence_updates_additional_queries');
    	function credence_updates_additional_queries($queryArgs) {
			$license_key = 'not_applicable';
        	$queryArgs['license_key'] = $license_key;
        	$queryArgs['market'] = 'mojo';
        	return $queryArgs;
    	}

		/** 
    	* Include the TGM_Plugin_Activation class. 
    	*/
		require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', array(&$this, 'shcreate_register_required_plugins') );

		/**
		 * Include our demo import admin page
		 */
		require_once dirname( __FILE__) . '/theme-import-admin.php';
		new theme_import_admin();

		/**
		 * Include our meta boxes
		 */
		require_once dirname( __FILE__ ) . '/theme-meta.php';
		new Theme_metabox();

		// Include our top primary menu
		add_action('init', array($this, 'register_top_menu'));

		// Include the layered top small menu
		add_action('init', array($this, 'register_top_minor_menu'));

		// Include our top secondary menu
		add_action('init', array($this, 'register_top_secondary'));

		// Include our Main Side Menu
		add_action('init', array($this, 'register_main_side_menu'));

		// Include the offcanvas sidebar
		add_action('init', array($this, 'register_side_offcanvas'));

		// Bottom menu
		add_action('init', array($this, 'register_footer_menu'));

		/* Plugin integration */

		// Turn auto updates off for layerslider
    	add_action('layerslider_ready', 'my_layerslider_overrides');
    	function my_layerslider_overrides() {
        	// Disable auto-updates
        	$GLOBALS['lsAutoUpdateBox'] = false;
    	}

		// Turn auto updates off for Revolution Slider
		if(function_exists('set_revslider_as_theme')) {
			add_action( 'init', array($this,'revslider_notifications_off'));
		}

 		// Force Visual Composer to initialize as "built into the theme". 
		if(function_exists('vc_set_as_theme')) vc_set_as_theme( $disable_updater = true );

		// Get rid of Redux advertisement in tools menu
		add_action( 'admin_menu', array($this, 'remove_redux_menu'),12 );

	} // end construct

	/*
	 * Remove annoying Redux tools menu items
	 */
	public function remove_redux_menu() {
		remove_submenu_page('tools.php', 'redux-about');
	}

	/*
	 * Turn off Revslider notifications
	 */
	public function revslider_notifications_off() {
		set_revslider_as_theme();
	}

	/*
	 *  Theme Support items after theme setup  -- todo: needs to be tied into variable small-logo with theme
	 */ 
	/*
	public function theme_support_items() {
		global $shcreate;
		global $wp_version;
		if ( version_compare( $wp_version, '3.4', '>=' ) ) {
			$args = array(
				'default-image' => get_template_directory_uri() . 'images/small-logo.png',
				'width' => '255',
				'height' => '52',
				'header-text' => false,
				'wp-head-callback' => '_custom_background_cb',
			);
			add_theme_support('custom-header', $args);

			//add_theme_support('custom-background', $args);
		}
	}
	*/

	/*
    * Enqueue admin scripts
    */
	public function load_custom_wp_admin_style() {
        wp_enqueue_media();
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style("wp-jquery-ui-dialog");  // styles for dialog

        wp_register_script( 'custom-admin-script', get_template_directory_uri() 
				. '/theme/admin/js/theme-admin.js', false, '1.0.0' );
        wp_enqueue_script( 'custom-admin-script' );
		wp_localize_script(  'custom-admin-script', 'credenceAdmin', array(
            'adminUrl'       => admin_url('admin-ajax.php'),
        ));
		wp_register_script( 'custom-admin-script2', get_template_directory_uri()
		        . '/theme/admin/js/theme-admin-icons.js', false, '1.0.0' );
		wp_enqueue_script( 'custom-admin-script2' );
        wp_register_style( 'custom-admin-css', get_template_directory_uri() . '/theme/admin/css/theme-admin.css', false, '1.0.0' );
        wp_enqueue_style( 'custom-admin-css' );
        // Font awesome
        wp_enqueue_style( 'font-awesome',
                get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css',false, '', false);
    }


	/*
	 * Retina Images
	*/
	public function retina_support_attachment_meta( $metadata, $attachment_id ) {
    	foreach ( $metadata as $key => $value ) {
        	if ( is_array( $value ) ) {
            	foreach ( $value as $image => $attr ) {
                	if ( is_array( $attr ) ) {
						/* Added check so we don't generate @2x images of @2x images...kinda redundant */
						$check2x = get_attached_file( $attachment_id );
						if(strpos($check2x, '@2x')===false) {
                    		$this->retina_support_create_images( get_attached_file( $check2x ), 
								$attr['width'], $attr['height'], true );
						}
					}
            	}
        	}
    	}
   		return $metadata;
	}

	/**
 	* Create retina-ready images
 	*
 	* Referenced via retina_support_attachment_meta().
 	*/
	public function retina_support_create_images( $file, $width, $height, $crop = false ) {
    	if ( $width || $height ) {
        	$resized_file = wp_get_image_editor( $file );
        	if ( ! is_wp_error( $resized_file ) ) {
            	$filename = $resized_file->generate_filename( $width . 'x' . $height . '@2x' );
 
            	$resized_file->resize( $width * 2, $height * 2, $crop );
            	$resized_file->save( $filename );
 
            	$info = $resized_file->get_size();
 
            	return array(
                	'file' => wp_basename( $filename ),
                	'width' => $info['width'],
                	'height' => $info['height'],
            	);
        	}
    	}
    	return false;
	}

	/**
 	* Delete retina-ready images
 	*
 	* This function is attached to the 'delete_attachment' filter hook.
 	*/
	public function delete_retina_support_images( $attachment_id ) {
    	$meta = wp_get_attachment_metadata( $attachment_id );
    	$upload_dir = wp_upload_dir();
		if (isset($meta['file'])) {
    		$path = pathinfo( $meta['file'] );
    		foreach ( $meta as $key => $value ) {
        		if ( 'sizes' === $key ) {
            		foreach ( $value as $sizes => $size ) {
                		$original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
                		$retina_filename = substr_replace( $original_filename,'@2x.',strrpos( $original_filename, '.' ),strlen( '.' ) );
                		if ( file_exists( $retina_filename ) ) {
                    		unlink( $retina_filename );
						}
            		}
        		}
    		}
		}
	}

	/*
	 * Register top menu
	 */
	public function register_top_menu() {
        register_nav_menu( 'nav-menu', __( 'Navigation Menu', 'shcreate' ) );
    }

	/*
	 * Register top minor menu
	 */
	public function register_top_minor_menu() {
		register_nav_menu( 'top-minor-menu', __('Top Layered Minor Menu', 'shcreate' ) );
	}

	/*
	 * Register Bottom menu
	 */
	public function register_footer_menu() {
		register_nav_menu( 'footer-menu', __( 'Footer Menu', 'shcreate' ) );
    }

	/*
	 * Register secondary main menu
	 */
	public function register_top_secondary() {
		register_nav_menu( 'nav-menu-secondary', __('Secondary Navigation Menu', 'shcreate' ) );
	}

	/* 
	 * Register Main Side Menu
	 */
	public function register_main_side_menu() {
		register_nav_menu( 'main-side-menu', __('Main Side Menu', 'shcreate') );
	}

	/*
	 * Register off canvas sidebar menu
	 */
	public function register_side_offcanvas() {
		register_nav_menu( 'side-menu', __('Off Canvas Sidebar', 'shcreate' ) );
	}

	/*
	 * Use wp_remote_get
	 */
	public function load_remote($url) {
		$result = wp_remote_get(
			$url,
			array(
				'timeout'  => 600
			)
		);
		return $result;
	}

	/*
	 * Import Demo Content
	 */
	public function import_demo_content() {
		if (!wp_verify_nonce( $_POST['nonce'], "import_demo_content")) {
			exit("Invalid Request");
		}


		$demo_choice = intval($_POST['demo_choice']);
		// 1 is Main, 2 is single page
		if (!class_exists("WP_Import")) {
            if (!defined("WP_LOAD_IMPORTERS")) define("WP_LOAD_IMPORTERS",true);
            require_once(dirname(__FILE__) . '/inc/importer/wordpress-importer.php');
            require_once(dirname(__FILE__) . '/theme-import.php');
        }

        $this->importer = new CTImport();
        $this->importer->fetch_attachments = true;

        $response = $this->importer->getDemo($demo_choice);
        //$this->importer->import($this->demo_file);

		$jsonData = array (
			'type'            => 'success',
			'success'         => $this->importer->success,
			'options_success' => $this->importer->options_success,
			'response'        => $this->importer->response
		);
		echo json_encode($jsonData);
		die();
	}

	/*
	 * TGM register plugins function
	 */
	public function shcreate_register_required_plugins() {
		$storage = 'http://credence-plugins.sh-themes.com/';
		$plugins = array(
			// Visual Composer
			array(
				'name'     				=> 'Visual Composer Page Builder', // The plugin name
				'slug'     				=> 'js_composer', // The plugin slug (typically the folder name)
				'source'                => $storage . 'js_composer.zip',
				'required' 				=> false, // If false, the plugin is only 'recommended' instead of required
				'version' 				=> '4.11.2.1',
				'force_activation' 		=> false, 
				'force_deactivation' 	=> false, 
				'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
			),
			// Layer Slider
			array(
				'name'					=> 'LayerSlider WP',
				'slug'					=> 'LayerSlider',
				'source'                => $storage . 'layersliderwp.zip',
				'required'				=> false,
				'version'				=> '5.6.8',
				'force_activation'   	=> false,
				'force_deactivation'	=> false,
				'external_url'			=> ''
			),
			// Slider Revolution
			array(
                'name'                  => 'Slider Revolution',
                'slug'                  => 'revslider',
				'source'                => $storage . 'revslider.zip',
                'required'              => false,
                'version'               => '5.2.5.2',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'external_url'          => ''
            ),
			// SH-Shortcodes
			array(
                'name'                  => 'SH Shortcodes', // The plugin name
                'slug'                  => 'sh-shortcodes', // The plugin slug (typically the folder name)
				'source'                => $storage . 'sh-shortcodes.zip',
                'required'              => true, // If false, the plugin is only 'recommended' instead of required
                'version'               => '1.3.4',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'external_url'          => '', // If set, overrides default API URL and points to an external URL
            ),
			// Screets Live Chat
			array(
				'name'                  => 'Live Chat', // The plugin name
                'slug'                  => 'screets-lc', // The plugin slug (typically the folder name)
				'source'                => $storage . 'Live_Chat.zip',
                'required'              => false, // If false, the plugin is only 'recommended' instead of required
                'version'               => '2.0.4',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'external_url'          => '', // If set, overrides default API URL and points to an external URL
			),
			// Essential Grid
			array(
                'name'                  => 'Essential Grid', // The plugin name
                'slug'                  => 'essential-grid', // The plugin slug (typically the folder name)
				'source'                => $storage . 'essential-grid.zip',
                'required'              => false, // If false, the plugin is only 'recommended' instead of required
                'version'               => '2.0.9.1',
                'force_activation'      => false,
                'force_deactivation'    => false,
                'external_url'          => '', // If set, overrides default API URL and points to an external URL
            ),

		);

		$config = array(
        	'id'           => 'tgmpa_credence',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        	'default_path' => '',                      // Default absolute path to bundled plugins.
        	'menu'         => 'tgmpa-install-plugins', // Menu slug.
        	'parent_slug'  => 'themes.php',            // Parent menu slug.
        	'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        	'has_notices'  => true,                    // Show admin notices or not.
        	'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        	'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        	'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        	'message'      => '',                      // Message to output right before the plugins table.
		);
		tgmpa( $plugins, $config );
	}
}
