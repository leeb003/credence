<?php

if (is_admin()) {
    require_once get_template_directory() . '/theme/admin/theme-admin.php';
    new themeAdmin();
}

require_once get_template_directory() . '/theme/theme-settings.php';
$themeSettings = new ThemeSettings();
if ( ! isset( $content_width ) ) {
   	$content_width = $themeSettings->config['content_width'];
}

// Init theme functions
	require_once get_template_directory() . '/theme/theme-init.php';
	new themeInit;


/**
	ReduxFramework 
**/

if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/../framework/ReduxCore/framework.php' ) ) {
    require_once( get_template_directory() . '/framework/ReduxCore/framework.php' );
}

/**
  Add Extensions before loading config
**/
require_once( get_template_directory() . '/framework/ReduxCore/sh-themes/config.php' );

if ( !class_exists( "Redux_Framework_config" ) ) {
	add_action('init', 'initialize_Redux_frame');
	/* Removed straight call for buddy press warning and load with init */
	//require_once get_template_directory() . '/theme/framework-config.php';
	//new Redux_Framework_config();
}

/**
   Initialize our framework configuration (relocated from above for BuddyPress)
**/
function initialize_Redux_frame() {
	require_once get_template_directory() . '/theme/framework-config.php';
	// sets to global so we can change options theoretically anywhere now (using in import to clean out any accidental
	// variables set (e.g. style-switcher)
	// https://github.com/reduxframework/redux-framework/wiki/Update-an-option-outside-the-options-panel
	global $framework_config;
	$framework_config = new Redux_Framework_config();
}

/**
	Add Font Awesome icons to framework
**/
function newIconFont() {
    wp_register_style(
        'redux-font-awesome',
		get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css',
        array(),
        time(),
        'all'
    ); 
    wp_enqueue_style( 'redux-font-awesome' );
}
add_action( 'redux/page/redux_demo/enqueue', 'shcreate' );

