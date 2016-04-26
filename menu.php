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

<?php 
	// add the top header if selected
	if (isset($shcreate['above-nav']) && $shcreate['above-nav'] == 1) {
?>
	<div class="above-nav">
		<div class="nested-container">
			<div class="row">
			<?php if ($shcreate['header-text-side'] == 2) { // text on right ?>
				<div class="col-md-6 text-left">
				<?php if (isset($shcreate['opt-multi-social'][0])) { ?>
                    <ul class="above-social">
                    <?php foreach ($shcreate['opt-multi-social'] as $k => $v) { ?>
                        <li>
                            <a href="<?php echo $v;?>" target="_blank">
                                <i class="<?php echo $shcreate['opt-multi-fa'][$k];?>"></i>
                            </a>
                        </li>
                    <?php } ?>
                    </ul>
                <?php } ?>
                </div>
				<div class="col-md-6 text-right">
                    <?php echo $shcreate['header-text']; ?>
                </div>

			<?php } else { ?>

				<div class="col-md-6">
					<?php echo $shcreate['header-text']; ?>
				</div>
				<div class="col-md-6 text-right">
				<?php if (isset($shcreate['opt-multi-social'][0])) { ?>
               		<ul class="above-social">
                   	<?php foreach ($shcreate['opt-multi-social'] as $k => $v) { ?>
                   		<li>
                       		<a href="<?php echo $v;?>" target="_blank">
                           		<i class="<?php echo $shcreate['opt-multi-fa'][$k];?>"></i>
                           	</a>
                   		</li>
					<?php } ?>
					</ul>
           		<?php } ?>
				</div>

			<?php } // end text side check ?>
			</div>
		</div>
	</div>

<?php } else { // end above nav section and add 1px spacer for navbar sticky  ?>
	<div class="no-above" style="height:1px;"></div>
<?php } ?>

<?php // main menu
$style = '';
$menu_name = $themeSettings->menu_check();  // Make sure our menu exists
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
			if ($shcreate['nav-option'] == '2'
				|| $shcreate['nav-option'] == '5' ) {  // center logo layered
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
<?php 
	$top_shadow = '';
	// Page shadow override
	if (isset($meta['enable_menushadow']) && $meta['enable_menushadow'][0] == 'yes') {
		if ($meta['menushadow'][0] == 'show') {
			$top_shadow = 'top-shadow-section';
		} 
	} elseif (isset($shcreate['top-shadow-section']) && $shcreate['top-shadow-section'] == 'yes') {
		// theme options if page override isn't set
		$top_shadow = 'top-shadow-section';
	}
?>
	<div class="top-holder <?php echo $top_shadow; ?>">
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

					<?php // top minor menu for layers
					$showminor = false;
					if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
						if ($meta['menubar'][0] != 'classic') {
							$showminor = true;
						}
					} elseif ($shcreate['nav-option'] == '2' 
						|| $shcreate['nav-option'] == '3'
						|| $shcreate['nav-option'] == '4'
					) { // If Not the classic menu
						$showminor = true;
					}

					if ($showminor == true) {
						echo '<div class="top-minor-container">';
                        wp_nav_menu( array(
                            'theme_location' => 'top-minor-menu',
                            'container' => false,
                            'depth' => 1,
                            'menu_class' => 'top-minor-menu hidden-sm hidden-xs'
                        ));
						echo '</div>';
                    } ?>
        		</div>
				<div class="collapse navbar-collapse nav-header-collapse">
					<?php if ($menu_name) { ?>
						<?php wp_nav_menu( array( 
                   			'theme_location' => 'nav-menu',
                   			'container' => false,
							'depth' => 4,
                   			'menu_class' => 'nav navbar-nav',
                   			'walker' => new wp_bootstrap_navwalker(),
                   			'walker_arg' => $current
               			)); ?>
					<?php } else { ?>
						<div class="msg">Please make sure your menu has a check mark next to 'Navigation Menu' </div>
					<?php } ?>


					<?php // secondary nav menu
					$showsecondary = false;
					if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
                        if ($meta['menubar'][0] != 'classic') {
                            $showsecondary = true;
                        }
                    } elseif ($shcreate['nav-option'] != '1') { // If Not the classic menu
						$showsecondary = true;
					}
					if (true == $showsecondary) {	
						wp_nav_menu( array(
                    		'theme_location' => 'nav-menu-secondary',
                    		'container' => false,
                    		'depth' => 1,
                    		'menu_class' => 'nav-menu-secondary'
                    	));
					} ?>

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
