<?php
global $shcreate;
global $themeSettings;
if ($shcreate['small-logo']['url']) {
    $image = '<img src="' . $shcreate['small-logo']['url'] . '" alt="small logo" />';
} else {
    $image = '';
}
/* Theme overrides for pages */
$page_id = get_queried_object_id();
$meta    = get_post_meta($page_id);
?>

<?php // main menu
$style = '';
$menu_name = $themeSettings->main_side_check();  // Make sure our menu exists
if ($menu_name) {
	get_template_part('theme/wp_bootstrap_navwalker');
    $current = get_page_template_slug();
	// Menu Page overrides
	if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
    	if ($meta['menubar'][0] == 'centerlayered') {
        	$style = 'center-logo-above';
    	} else if ($meta['menubar'][0] == 'leftlayered') {
			$style = 'left-logo';
		} else if ($meta['menubar'][0] == 'rightlayered') {
			$style = 'right-logo';
		}

	} else { // theme option settings
		$style = '';
		if ($menu_name) {
			if ($shcreate['nav-option'] == '2') {  // center logo layered
				$style = 'center-logo-above';
			} elseif ($shcreate['nav-option'] == '3') {  // left logo layered
				$style = 'left-logo';
			} elseif ($shcreate['nav-option'] == '4') { // right logo layered
				$style = 'right-logo';
			}
		}
	}
}
?>
	<div class="side-nav-small">
		<div class="navbar-bg-col">
			<nav class="navbar yamm navbar-default <?php echo $style; ?>" role="navigation">
    			<div class="navbar-header">
        			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-header-collapse">
           				<span class="sr-only">Toggle navigation</span>
           				<span class="icon-bar"></span>
           				<span class="icon-bar"></span>
           				<span class="icon-bar"></span>
          			</button>
          			<a href="<?php echo home_url() ?>/#" class="navbar-brand"> <?php echo $image;?></a>

        		</div>
				<div class="collapse navbar-collapse nav-header-collapse">
					<?php if ($menu_name) { ?>
						<?php wp_nav_menu( array( 
                   			'theme_location' => 'main-side-menu',
                   			'container' => false,
							'depth' => 2,
                   			'menu_class' => 'nav navbar-nav',
                   			'walker' => new wp_bootstrap_navwalker(),
                   			'walker_arg' => $current
               			)); ?>
					<?php } else { ?>
						<div class="msg">Please make sure your menu has a check mark next to 'Main Side Menu' </div>
					<?php } ?>

        		</div><!--/.nav-collapse -->
			</nav>
		</div>
		<!-- Search form -->
		<div class="top-search-holder">
			<div class="row">
				<div class="col-md-12">
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>
