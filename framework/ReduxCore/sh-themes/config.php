<?php

// INCLUDE THIS BEFORE you load your ReduxFramework object config file.


// You may replace $redux_opt_name with a string if you wish. If you do so, change loader.php
// as well as all the instances below.
$redux_opt_name = "shcreate";


// Add icon select
function add_icon_select($sections) {
    //$sections = array();
    $sections[7]['fields'][] = array(
        'id'=>'icon_select',
        'type' => 'icon_select', 
        //'required' => array('switch-fold','equals','0'),  
        'title' => __('Icon Select', 'redux-framework-demo'),
        'subtitle'  => __('Select an icon.', 'redux-framework-demo'),
        //'default'     => '',
        //'options' => array(), // key/value pair, value is the title
        //'enqueue' => false, // Disable auto-enqueue of stylesheet if present in the panel
        //'enqueue_frontend' => false, // Disable auto-enqueue of stylesheet on the front-end
        //'stylesheet' => 'http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css', // full path OR url to stylesheet
        //'prefix' => 'fa', // If needed to initialize the icon
        //'selector' => 'fa-', // How each icons begins for this given font
        //'height' => 300 // Change the height of the container. defaults to 300px;
    );

    return $sections;
}
add_filter("redux/options/{$redux_opt_name}/sections", 'add_icon_select' );


// The loader will load all of the extensions automatically based on your $redux_opt_name
require_once(dirname(__FILE__).'/loader.php');
