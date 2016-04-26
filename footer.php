<?php
global $shcreate;
global $themeSettings;

// check for template 
if (is_page_template()) {
	$sh_template = basename ( get_post_meta( get_the_id(), '_wp_page_template', true ) );
} else {
    $sh_template = '';
}
?>

<?php if ($sh_template != 'single-blank.php') { //don't show the footers on blank pages ?>

		<a href="#" class="upToTop toTop">
			<i class="fa fa-angle-up"> </i>
		</a>
		<footer>
			<?php if ($shcreate['footerw-enable'] == 1) { // only if enabled in theme options ?>
			<div class="row footer-widget-holder">
				<div class="nested-container">
					<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer') ) // footer widget area ?>
				</div>
			</div>
			<?php } ?>
			<?php if ($shcreate['footer-enable'] == '1'
					&& $shcreate['footer-center-text'] == '1') { // only if enabled in theme options and not centered  ?>
			<div class="footer-holder">
				<div class="nested-container">
					<div class="row footer">
						<div class="col-md-6">
							<?php echo $shcreate['footer-text'];?>
						</div>

				    	<div class="col-md-6 text-right">
							<div class="footer-menu-holder">
                        		<?php $footer_name = $themeSettings->footer_menu_check();  // Make sure our footer menu exists ?>
                            	<?php if ($footer_name) { ?>
                                	<?php wp_nav_menu( array(
                                    	'theme_location' => 'footer-menu',
                                    	'container' => false,
                                    	'depth' => 1,
                                    	'menu_class' => 'footer-menu'
                                	)); ?>
                            	<?php } else { ?>
                                	<!-- <div class="msg">Please make sure your menu has the footer menu set up. </div> -->
                            	<?php } ?>
                        	</div>
						</div>

					</div>
				</div>
			</div>
			<?php } elseif ($shcreate['footer-enable'] == '1' && $shcreate['footer-center-text'] == '2') { // centered ?>
			<div class="footer-holder">
				<div class="nested-container">
					<div class="row footer-centered">
						<div class="col-md-12">
							<?php echo $shcreate['footer-text'];?>
						</div>
					</div>
				</div>
			</div>
			<?php } // end centered footer ?>
		</footer>

<?php } // end blank page check ?>

	</div> <!-- end wrapper -->
</div> <!-- end container -->

<?php
// check for sidemenu option
if ($shcreate['top-side-menu'] == '2'
  && $sh_template != 'single-blank.php') {
?>
	</div><!-- end content-holder -->
	<?php
	// right menu (left menu in header)
    if ($shcreate['top-side-menu'] == '2'
        && $shcreate['side-menu-location'] == '2') { 
		if ($sh_template != 'single-blank.php') { //don't show the menus on blank pages 
			 if ($shcreate['small-logo']['url']) {
                $logo_image = '<img src="' . $shcreate['small-logo']['url'] . '" alt="small logo" />';
            } else {
                $logo_image = '';
            }

    ?>
        <div class="main-side right-navigation">
			<div class="side-logo"><a href="<?php echo home_url() ?>/#"><?php echo $logo_image; ?></div></a>

			<?php $side_menu = $themeSettings->main_side_check();  // Make sure our menu exists ?>
            <?php if ($side_menu) { ?>
                <?php wp_nav_menu( array(
                    'theme_location' => 'main-side-menu',
                    'container' => false,
                    'depth' => 2,
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
	</div><!-- end side-wrapper -->
	<?php } ?>
<?php } ?>

<?php
// style switcher for demos only
if (isset($shcreate['style-switcher']) && $shcreate['style-switcher'] == '1') { ?>
    <div id="style-switcher" class="style-switcher-hidden">
        <div class="style-switcher-top"><b>CHANGE STYLES</b> <i class="fa fa-gears"></i></div>
        <div class="style-switcher-body">
			<div class="feat-title"><span>Site Layouts</span> 
				<div class="sh-popover" data-trigger="hover" data-placement="right" data-custclass="switcher-popover" 
				data-original-title="Layouts" data-content="Choose Full Width, Boxed or Boxed Offset and set any color, repeat or full image as your backgrounds.">
				<span class="info fa fa-info-circle"></span>
			</div>
		</div>
            <select class="ss-layout" autocomplete="off">
				<option value="wide" selected="selected">Wide</option>
                <option value="boxed">Boxed</option>
				<option value="boxed-offset">Boxed Offset</option>
            </select>

			<div class="ss-sep"></div>
			<?php
                $current_site = get_site_url();
                if ($current_site != 'http://demo.sh-themes.com/credencedark') {
            ?> 
            <div class="feat-title">
                <span>Navigation</span> 
                <div class="sh-popover" data-trigger="hover" data-placement="right" 
                    data-custclass="switcher-popover" data-original-title="Navigational Colors" 
                    data-content="Above Navigation, Navigation Bar and menus below can all have any colors set independent of each other.">
                    <span class="info fa fa-info-circle"></span>
                </div>
            </div>

            <select class="ss-header" autocomplete="off">
                <option value="light" selected="selected">Light</option>
                <option value="dark">Dark</option>
            </select>
            <div class="ss-sep"></div>
            <?php } // end check for dark theme ?>
			<div class="ss-pagecolor" style="display:none;">
				<div class="feat-title">
                	<span>Page Colors</span> 
                	<div class="sh-popover" data-trigger="hover" data-placement="right" 
                    	data-custclass="switcher-popover" data-original-title="Page Colors" 
                    	data-content="Above Navigation, Navigation Bar and menus below can all have any colo.">
                    	<span class="info fa fa-info-circle"></span>
                	</div>
            	</div>
				
				<a href="#" class="ss-pc"><span class="page-color white"></span></a>
				<a href="#" class="ss-pc"><span class="page-color dark"></span></a>
				<a href="#" class="ss-pc"><span class="page-color blue"></span></a>
				<a href="#" class="ss-pc"><span class="page-color emerald"></span></a>
				<a href="#" class="ss-pc"><span class="page-color carrot"></span></a>
				<a href="#" class="ss-pc"><span class="page-color amethyst"></span></a>
				<a href="#" class="ss-pc"><span class="page-color silver"></span></a>
			</div>
				
			<div class="ss-accent">
				<div class="feat-title">
                	<span>Accent Colors</span> 
                	<div class="sh-popover" data-trigger="hover" data-placement="right" 
                    	data-custclass="switcher-popover" data-original-title="Accent Colors" 
                    	data-content="These are just a few samples of unlimited colors in Credence.  Every Section of the site can have it's own colors.  Most elements can also override theme colors.">
                    	<span class="info fa fa-info-circle"></span>
                	</div>
            	</div>

				<a href="javascript:chooseStyle('alizarin-theme', 60)" class="ss-acc">
                    <span class="accent-color alizarin"></span>
                </a>
				<a href="javascript:chooseStyle('blue-theme', 60)" class="ss-acc">
                    <span class="accent-color blue"></span>
                </a>
				<a href="javascript:chooseStyle('emerald-theme', 60)" class="ss-acc">
					<span class="accent-color emerald"></span>
				</a>
				<a href="javascript:chooseStyle('carrot-theme', 60)" class="ss-acc">
					<span class="accent-color carrot"></span>
				</a>
				<a href="javascript:chooseStyle('amethyst-theme', 60)" class="ss-acc">
                    <span class="accent-color amethyst"></span>
                </a>
				<a href="javascript:chooseStyle('silver-theme', 60)" class="ss-acc">
                    <span class="accent-color silver"></span>
                </a>
				<a href="javascript:chooseStyle('sunflower-theme', 60)" class="ss-acc">
                    <span class="accent-color sunflower"></span>
                </a>
			</div>
        </div>
    </div>
<?php } ?>


    <?php wp_footer(); ?>

	<?php /* User generated js, css, and tracking codes */ ?>
	<?php if (isset($shcreate['js-code']) && trim($shcreate['js-code']) != '') { ?>
    <script>
        <?php echo $shcreate['js-code']; // User javascript ?>
    </script>
    <?php } ?>

    <?php if (isset($shcreate['css-code']) && trim($shcreate['css-code']) != '') { ?>
    <style>
        <?php echo $shcreate['css-code']; //User code ?>
    </style>
    <?php } ?>

    <?php 
		if (isset($shcreate['tracking-code'])) {
			echo html_entity_decode($shcreate['tracking-code']); // tracking code 
		} ?>

  </body>
</html>
