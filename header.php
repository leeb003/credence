<!DOCTYPE html>
<?php
global $shcreate;
global $themeSettings;

/* Theme overrides for pages */
if (function_exists( 'is_woocommerce' ) && is_woocommerce()) {
	// overrides for shop page apply to products and product details as well.  My Account, Cart, and Checkout
	// overrides work as expected
	$page_id = get_option('woocommerce_shop_page_id');
} else {
	$page_id = get_queried_object_id();
}
$meta    = get_post_meta($page_id);
// Results per page
if (isset($meta['enable_width']) && $meta['enable_width'][0] == 'yes') {
	if ($meta['page_width'][0] == 'wide') {
		$container = 'container-fluid';
	} else {
		$container = 'container';
	}
} else { // normal theme options progression
	if ($shcreate['layout'] == 1) {
		$container = 'container-fluid';
	} else {
		$container = 'container';
	}
}
?>
<html <?php language_attributes(); ?>>
	<head>
    	<meta charset="utf-8">
		<?php
        if ( ! function_exists( '_wp_render_title_tag' ) ) {
            function theme_slug_render_title() {
        ?>
                <title><?php wp_title(); ?></title>
            <?php
            }

            function page_title_text( $title ) {
                if (function_exists( 'is_woocommerce' ) && is_woocommerce()) {
                    $wooTitle = woocommerce_page_title(false) . ' | ' . get_bloginfo( 'name' );
                    return $wooTitle;
                } else {
                    return get_the_title() . ' | ' . get_bloginfo( 'name' );
                }
            }
            add_action( 'wp_head', 'theme_slug_render_title' );
            add_filter( 'wp_title', 'page_title_text' );
        }
        ?>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    	<!-- styles -->
    	<link href="<?php bloginfo('stylesheet_url');?>" rel="stylesheet">
    	<?php 
        $favicon = $shcreate['favicon']['url'];
        if ($favicon != '') {?>
    	<link rel="shortcut icon" href="<?php echo $favicon;?>" />
    	<?php } ?>

		<?php // check for template first or errors
		if (is_page_template()) {
			$sh_template = basename ( get_post_meta( get_the_id(), '_wp_page_template', true ) ); 
		} else {
			$sh_template = '';
		}

		?>

		<!-- wp_head() -->
   		<?php wp_head(); ?>
		<?php 
			// Layout modification for offset theme with top overrides and vice versa
			if (isset($meta['enable_width']) && $meta['enable_width'][0] == 'yes') {
				if ($meta['page_width'][0] == 'boxedoffset') { 
					echo '<style>.above-nav, .no-above { margin-top: 60px; } </style>'; 
				} else { 
					echo '<style> .above-nav, .no-above { margin-top: 0px; } </style>'; 
				}
			}
		?>
  	</head>
	<body <?php body_class(); ?>>

	<?php
	// check for sidemenu option
	if ($shcreate['top-side-menu'] == '2'
		&& $sh_template != 'single-blank.php') { //don't show the menus on blank pages
		// get the small menu for side menus to load
		get_template_part("side-menu-small"); 
	?>
		
		<div class="side-wrapper">
	<?php }
	// left menu (right menu in footer)
	if ($shcreate['top-side-menu'] == '2'
		&& $shcreate['side-menu-location'] == '1') { 
			if ($sh_template != 'single-blank.php') { //don't show the menus on blank pages 
				if ($shcreate['small-logo']['url']) {
    				$logo_image = '<img src="' . $shcreate['small-logo']['url'] . '" alt="small logo" />';
				} else {
    				$logo_image = '';
				}
	?>
		<div class="main-side left-navigation">
			<div class="side-logo"><a href="<?php echo home_url() ?>/#"><?php echo $logo_image; ?></div></a>

			<?php $side_menu = $themeSettings->main_side_check();  // Make sure our menu exists ?>
			<?php if ($side_menu) { ?>
            	<?php wp_nav_menu( array(
                	'theme_location' => 'main-side-menu',
                    'container' => false,
                    'depth' => 1,
                    'menu_class' => 'side-nav-menu',
                )); ?>
            <?php } else { ?>
            	<div class="msg">Make sure your menu has a check mark next to 'Main Side Menu' </div>
            <?php } ?>

			<?php if (isset($shcreate['opt-multi-social'][0])) { ?>
                <ul class="side-social">
                <?php foreach ($shcreate['opt-multi-social'] as $k => $v) { ?>
                    <li>
                        <a href="<?php echo $v;?>" target="_blank">
                            <i class="<?php echo $shcreate['opt-multi-fa'][$k];?>"></i>
                        </a>
                    </li>
                <?php } ?>
                </ul>
            <?php } ?>
			<div class="side-text">
				<?php echo do_shortcode($shcreate['side-menu-text']); ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>

	<?php // content holder for main content with side menus 
	if ($shcreate['top-side-menu'] == '2'
		&& $sh_template != 'single-blank.php') { //don't show the menus on blank pages ?>

	<div class="content-holder">
	<?php } ?>

	<div class="<?php echo $container;?>">
		<div class="wrapper">
			<div id="top"></div>
			<!--Loading Container-->	
			<div id="loader">
				<div id="loader-holder">
					<!-- <img src="<?php echo get_template_directory_uri() . '/images/loading.gif';?>" alt="" /><h5>loading</h5> -->
				</div>
			</div>

			<?php if ($shcreate['top-side-menu'] == '1') { // don't show top menus when side menu is selected ?>
				<?php if ($sh_template != 'single-blank.php') { //don't show the menus on blank pages ?>
					<?php if ($shcreate['top-slider'] == 1) { // Enable Top Menu ?>
                    <div id="top-slide">
                        <div class="row top-slide-widget">
                            <div class="top-slide-container">
                                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Topslide') ) // widget area ?>
                            </div>
                        </div>


                        <a class="top-slide-control" href="#">
                            <span>
                                <i class="fa fa-angle-down"></i>
                                <i class="fa fa-angle-up hide"></i>
                            </span>
                        </a>
                    </div>
                	<?php } ?>
					<?php get_template_part("menu"); ?>
				<?php } // end blank page check ?>
			<?php } // end side menu check ?>


