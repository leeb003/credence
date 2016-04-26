<?php 
wp_meta();
if ( (function_exists( 'is_woocommerce' ) && is_woocommerce())
	|| (function_exists( 'is_woocommerce' ) && is_cart())
){ 
	dynamic_sidebar( 'woocommerce' ); 
} else {
	!function_exists('dynamic_sidebar') || !dynamic_sidebar();
} 
