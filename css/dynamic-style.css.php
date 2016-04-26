<?php
/* 
 * Custom dynamic styles after stylesheets loaded, generates stylesheet from theme settings
 */
	class dynamic_style {
		// properties
		private $font;		
		private $font_css;
		private $menu_font_css;
		private $h1_css;
		private $h2_css;
		private $h3_css;
		private $h4_css;
		private $h5_css;
		private $h6_css;

		/*
		 * Return results
		 */
		public function __construct() {
			global $shcreate; // Theme Options

			$css = '/* Dynamically generated styles for the theme, this comes after our style.css */' . "\n\n";

			$get_template_directory_uri = get_template_directory_uri();
			//print_r($shcreate);
            $this->font = isset($shcreate['site-font']) ? $shcreate['site-font'] : '';
			if (!isset($shcreate['site-font'])) {
				$this->font['color'] = '';
			}
            $this->font_css = $this->output($this->font);
			$this->menu_font_css = isset($shcreate['menu-font']) ? $shcreate['menu-font'] : '';
			$this->menu_font_css = $this->output($this->menu_font_css); // format
			$this->h1_css = isset($shcreate['h1-font']) ? $shcreate['h1-font'] : '';
			$this->h1_css = $this->output($this->h1_css);  // format it
			$this->h2_css = isset($shcreate['h2-font']) ? $shcreate['h2-font'] : '';
			$this->h2_css = $this->output($this->h2_css);
			$this->h3_css = isset($shcreate['h3-font']) ? $shcreate['h3-font'] : '';
			$this->h3_css = $this->output($this->h3_css);
			$this->h4_css = isset($shcreate['h4-font']) ? $shcreate['h4-font'] : '';
			$this->h4_css = $this->output($this->h4_css);
			$this->h5_css = isset($shcreate['h5-font']) ? $shcreate['h5-font'] : '';
			$this->h5_css = $this->output($this->h5_css);
			$this->h6_css = isset($shcreate['h6-font']) ? $shcreate['h6-font'] : '';
			$this->h6_css = $this->output($this->h6_css);

			// Presets avoid warning errors
			$this->background_pattern = isset($shcreate['background-pattern']['url']) ? $shcreate['background-pattern']['url'] : '';
			if (isset($shcreate['footer-option']) && $shcreate['footer-option'] == 2) {
				$this->footer_background_pattern = isset($shcreate['footer-background-pattern']['url']) ? $shcreate['footer-background-pattern']['url'] : '';
			} else {
				$this->footer_background_pattern = '';
			}


			if (isset($shcreate['header-image']['url'])) { 
                $css .= '	.head-section { background-image:url("' . $shcreate['header-image']['url'] . '"); }' . "\n";
			}

			// boxed view background
			if (isset($shcreate['background-option'])) {
				if ($shcreate['background-option'] == 1) {
            		$background = 'background-color: ' . $shcreate['color-background'] . ';';
            	} elseif ($shcreate['background-option'] == 2) {
                	$background = 'background-image: url("' . $this->background_pattern . '");' . "\n";
					$background .= '	background-attachment: fixed;';
				} elseif ($shcreate['background-option'] == 3) {
					$background = 'background-image: url("' . $shcreate['background-image']['url'] . '");' . "\n";
					$background .= '	background-attachment: fixed;' . "\n";
					$background .= '	background-size: cover;';
				}

            } else { 
                $background = '';
            }

			// Main page background color
			if (isset($shcreate['main-bg-color'])) {
				$page_bg = $shcreate['main-bg-color'];
			}

			// Navbar bg color or image
			if (isset($shcreate['menu-bg-option'])) {
				if ($shcreate['menu-bg-option'] == 1) {
					$menu_bg = 'background-color: ' . $shcreate['menu-bg-color']['rgba'] . ';';
				} elseif ($shcreate['menu-bg-option'] == 2) {
					$menu_bg = 'background-image: url("' . $shcreate['menu-bg-pattern']['url'] . '");' . "\n";
				}
			}

			// Top Sticky Animation
			$top_sticky_css = '';
			if (isset($shcreate['top-sticky']) && $shcreate['top-sticky'] == 'yes') {
				if (isset($shcreate['top-sticky-anim']) && $shcreate['top-sticky-anim'] == 'yes') {
					$top_sticky_css = '.top-holder.stuck .navbar-nav > li > a,' . "\n"
									. '.top-holder.stuck .nav-menu-secondary > li a {' . "\n"
							    	. '		padding-left: 8px;' . "\n"
									. '		padding-right: 8px;' . "\n"
									. '}' . "\n";
				}
			}

			// Breadcrumb background image and font color
			$breadcrumb_bg = '';
			$breadcrumb_font = $shcreate['breadcrumb-font-color'];
			$breadcrumb_link = $shcreate['breadcrumb-link-color'];
			if (isset($shcreate['breadcrumb-option'])) {
				if ($shcreate['breadcrumb-option'] == 1) {
					if ($shcreate['breadcrumb-bg-option'] == 2) { // fixed background
						$breadcrumb_bg = 'background-image: url("' . $shcreate['breadcrumb-bg']['url'] . '");' . "\n"
									   . 'background-size: cover;' . "\n"
									   . 'background-repeat: no-repeat;' . "\n"
									   . 'background-attachment: fixed;' . "\n";

					} else if( $shcreate['breadcrumb-bg-option'] == 3) {  // standard fit, scroll
						$breadcrumb_bg = 'background-image: url("' . $shcreate['breadcrumb-bg']['url'] . '");' . "\n"
                                       . 'background-size: cover;' . "\n"
                                       . 'background-repeat: no-repeat;' . "\n";

					} else { // tiled background (option 1 or anything else)
						$breadcrumb_bg = 'background-image: url("' . $shcreate['breadcrumb-bg']['url'] . '");' . "\n";
					} 
				} elseif ($shcreate['breadcrumb-option'] == '2') {  // background color, no image
					$breadcrumb_bg = 'background-color: ' . $shcreate['breadcrumb-bg-color'] . ';';
				}
			}

			// Footer Widget bg color or image
			$footerw_bg = '';
			if (isset($shcreate['footerw-bg-option'])) {
				if ($shcreate['footerw-bg-option'] == 2) {
					$footerw_bg = 'background-color: ' . $shcreate['footerw-bg-color'];
				} elseif ($shcreate['footerw-bg-option'] == 3) {
					$footerw_bg = 'background-image: url("' . $shcreate['footerw-bg-pattern']['url'] . '");' . "\n";
				} elseif ($shcreate['footerw-bg-option'] == 4) {
                    $footerw_bg = 'background-image: url("' . $shcreate['footerw-bg-image']['url'] . '");' . "\n";
                    //$footerw_bg .= 'background-attachment: fixed;' . "\n";
					$footerw_bg .= 'background-repeat: no-repeat;';
                    $footerw_bg .= 'background-size: cover;';
                }
			}

			// Footer bg color or image
			$footer_bg = '';
			if (isset($shcreate['footer-bg-option'])) {
                if ($shcreate['footer-bg-option'] == 1) {
                    $footer_bg = 'background-color: ' . $shcreate['footer-bg-color'] . ';';
                } elseif ($shcreate['footer-bg-option'] == 2) {
                    $footer_bg = 'background-image: url("' . $shcreate['footer-bg-pattern'] . '");' . "\n";
                }
            }

			// Footer link overrides or standard accent color
			$footer_links = '';
			if (isset($shcreate['footer-override']) && $shcreate['footer-override'] == '2') { 
				$footer_links = <<<EOT
.footer a, 
.footer a:visited, 
.footer a:active,
.footer-centered a,
.footer-centered a:visited,
.footer-centered a:active {
        color: {$shcreate['footer-link-color']};
}
.footer a:hover, 
.footer a:focus,
.footer-centered a:hover,
.footer-centered a:focus {
		color: {$shcreate['footer-hover-color']};
}
EOT;
			}

			// Solid Accent color
                $accent_color = $this->hex2rgba($shcreate['color-accent'], $opacity = 1);
                $accent_color_light = $this->hex2rgba($shcreate['color-accent'], $opacity = 0.8);
				// negative values darken color (e.g. -0.5 ...50%) the lower the number darkens more
            	$accent_color_darker = $this->colorBrightness($shcreate['color-accent'], -0.9);

            // Opacity for font color elements
                $font_color = $this->hex2rgba($this->font['color'], $opacity = 1);
                $font_color_light = $this->hex2rgba($this->font['color'], $opacity = .25);

            // Opacity for Widget Font elements
                $footerw_font = $shcreate['footerw-font-color'];
				$footerw_font_light = $this->hex2rgba($shcreate['footerw-font-color'], $opacity = .25);

			// Above Navigation Offset option
            $above_offset = '';
            if (isset($shcreate['layout']) && $shcreate['layout'] == '3') {
                $above_offset = 'margin-top: 60px;';
            }

			// Above Navigation border choice
			$above_border = '';
            if (isset($shcreate['above-nav-border'])) {
                if ($shcreate['above-nav-border'] == '1') {
                    $above_border = 'border-top: 4px solid ' . $accent_color . ';';
                } 
			}

			// Navigation centered logo and centered primary menu
			$centered_primary = '';
			if (isset($shcreate['nav-option']) && $shcreate['nav-option'] == '5') {
				$centered_primary = <<<EOT
.navbar-nav {
	float:none !important;
}
.nav-menu-secondary {
	display:none;
}
EOT;
			}

			// Main Side Bar settings & styles
			$main_sidebar = '';
			$side_bg = '';
			if (isset($shcreate['top-side-menu']) && $shcreate['top-side-menu'] == '2') {
				if (isset($shcreate['side-menu-location']) && $shcreate['side-menu-location'] == '1') { // left menu
					$main_sidebar = <<<EOT
.side-wrapper {
	padding-left: 250px;
}

EOT;
				} elseif (isset($shcreate['side-menu-location']) && $shcreate['side-menu-location'] == '2') { // right menu
					$main_sidebar = <<<EOT
.side-wrapper {
	padding-right: 250px;
}

EOT;
				}

				// Alignment
				$side_align = 'center'; // default
				if (isset($shcreate['side-menu-align'])) {
					if ($shcreate['side-menu-align'] == '1') {
						$side_align = 'center';
					} elseif ($shcreate['side-menu-align'] == '2') {
                        $side_align = 'left';
					} elseif ($shcreate['side-menu-align'] == '3') {
                        $side_align = 'right';
					}
				}

				// Link Underline
				$side_underline = '';
				if (isset($shcreate['side-menu-underline']) && $shcreate['side-menu-underline'] == '2') {
					$side_underline = <<<EOT
.side-nav-menu > li > a {
	text-decoration: underline;
}
EOT;
				}
	
				if (isset($shcreate['side-bg-option'])) {
                	if ($shcreate['side-bg-option'] == 1) {
                    	$side_bg = 'background-color: ' . $shcreate['side-bg-color'] . ';' . "\n";
                	} elseif ($shcreate['side-bg-option'] == 2) {
                    	$side_bg = 'background-image: url("' . $shcreate['side-bg-image']['url'] . '");' . "\n";
						if ($shcreate['side-bg-choice'] == 2) {  // cover
                	    	$side_bg .= 'background-repeat: no-repeat;';
                    		$side_bg .= 'background-size: cover;';
						} 
                	}
            	}
				$main_sidebar .= <<<EOT
.main-side {
	color: {$shcreate['menu-font-color']};
	font-family: {$shcreate['side-text-font']['font-family']};
	font-weight: {$shcreate['side-text-font']['font-weight']};
	font-size: {$shcreate['side-text-font']['font-size']};
	line-height: {$shcreate['side-text-font']['line-height']};
	text-align: $side_align;
	$side_bg
}
.side-nav-menu {
	color: {$shcreate['menu-font-color']};
	font-family: {$shcreate['menu-font']['font-family']};
	font-weight: {$shcreate['menu-font']['font-weight']};
	font-size: {$shcreate['menu-font']['font-size']};
	line-height: {$shcreate['menu-font']['line-height']};
}
$side_underline
.main-side a,
.main-side a:visited {
	color: {$shcreate['side-menu-link']};
}
.main-side a:hover, .main-side a:focus {
	color: {$shcreate['side-menu-hover']};
}
.side-logo {
	margin-top: {$shcreate['logo-margin-top']['height']};
	margin-bottom: {$shcreate['logo-margin-bottom']['height']};
}
ul.side-social {
	margin-top: {$shcreate['sidesocial-margin-top']['height']};
	margin-bottom: {$shcreate['sidesocial-margin-bottom']['height']};
}
.side-text {
	margin-top: {$shcreate['side-text-margin-top']['height']};
	margin-bottom: {$shcreate['side-text-margin-bottom']['height']};
}

.side-nav-small > .navbar-bg-col {
	background-image: none;
	background-color: {$shcreate['side-bg-collapsed']};
}

.side-nav-small .navbar-default .navbar-toggle .icon-bar {
	background-color: {$shcreate['side-menu-link']};
}

.side-nav-small .navbar-default .navbar-nav > li > a {
	color: {$shcreate['side-menu-link']};
	font-family: {$shcreate['menu-font']['font-family']};
}

.side-nav-small .nav .open > a, .nav .open > a:hover,
.side-nav-small .nav .open > a:focus {
	background-color: {$shcreate['side-bg-collapsed']};
	color: {$shcreate['side-menu-link']};
}
   
.side-nav-small .dropdown-menu {
	background-color: {$shcreate['side-bg-collapsed']};
	color: {$shcreate['side-menu-link']};
}

.side-nav-small .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
.side-nav-small .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover,
.side-nav-small .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus,
.side-nav-small .navbar-default .navbar-nav > .open > a,
.side-nav-small .navbar-default .navbar-nav > .open > a:hover,
.side-nav-small .navbar-default .navbar-nav > .open > a:focus,
.side-nav-small .navbar-nav > li.menu-item:hover > a, 
.side-nav-small .navbar-nav > li.menu-item:focus > a  {
	background-color: {$shcreate['side-bg-collapsed']};
	color: {$shcreate['side-menu-hover']};
}
.side-nav-small .navbar-brand {
	height: 85px;
	padding-top: 5px;
}
.side-nav-small .navbar-brand > img {
	max-height: 75px;
}
.side-nav-small .navbar-toggle {
	margin-top: 25px;
}

EOT;
			}

// Top Slide down and above navigation font override
            $slidedown_font_fam = '';
            $abovenav_font_fam = '';
            if (isset($shcreate['slidedown-font-over']) && $shcreate['slidedown-font-over']) {
                $slidedown_font_fam = <<<EOT
.top-slide-container {
    font-family: {$shcreate['slidedown-font']['font-family']};
    font-weight: {$shcreate['slidedown-font']['font-weight']};
    font-size: {$shcreate['slidedown-font']['font-size']};
    line-height: {$shcreate['slidedown-font']['line-height']};
}
EOT;
            }
            if (isset($shcreate['abovenav-font-over']) && $shcreate['abovenav-font-over']) {
                $abovenav_font_fam = <<<EOT
.above-nav {
    font-family: {$shcreate['abovenav-font']['font-family']};
    font-weight: {$shcreate['abovenav-font']['font-weight']};
    font-size: {$shcreate['abovenav-font']['font-size']};
    line-height: {$shcreate['abovenav-font']['line-height']};
}
EOT;
            }

// Footer Widget and bottom Footer font override
			$footerw_font_fam = '';
			$footer_font_fam = '';
			if (isset($shcreate['footerw-font-over']) && $shcreate['footerw-font-over']) {
				$footerw_font_fam = <<<EOT
.footer-widget-holder {
    font-family: {$shcreate['footerw-font']['font-family']};
    font-weight: {$shcreate['footerw-font']['font-weight']};
    font-size: {$shcreate['footerw-font']['font-size']};
    line-height: {$shcreate['footerw-font']['line-height']};
}
EOT;
			}
			if (isset($shcreate['footer-font-over']) && $shcreate['footer-font-over']) {
                $footer_font_fam = <<<EOT
.footer-holder {
    font-family: {$shcreate['footer-font']['font-family']};
    font-weight: {$shcreate['footer-font']['font-weight']};
    font-size: {$shcreate['footer-font']['font-size']};
    line-height: {$shcreate['footer-font']['line-height']};
}
EOT;
			}

// Blog Sidebar font override
			$sidebar_font_fam = '';
			if (isset($shcreate['sidebar-font-over']) && $shcreate['sidebar-font-over']) {
                $sidebar_font_fam = <<<EOT
.sidebar {
    font-family: {$shcreate['sidebar-font']['font-family']};
    font-weight: {$shcreate['sidebar-font']['font-weight']};
    font-size: {$shcreate['sidebar-font']['font-size']};
    line-height: {$shcreate['sidebar-font']['line-height']};
}
EOT;
			}


/**************************************************************************************************/



		// MAIN OUTPUT
		$css .= <<<EOT
body {
	$this->font_css
	$background
}

$main_sidebar

.wrapper {
	background-color: $page_bg;
}

#loader {
	background-color: $page_bg;
}

.top-holder .navbar-nav > li > a,
.top-minor-menu > li > a {
    $this->menu_font_css;
}

.top-holder .navbar-brand {
	height: {$shcreate['logo-height']['height']};
	margin-top: {$shcreate['logo-margin-top']['height']};
    margin-bottom: {$shcreate['logo-margin-bottom']['height']};
}

.top-holder .navbar-brand > img {
	height: {$shcreate['logo-height']['height']};
	width: {$shcreate['logo-width']['width']};
}

.top-holder {
	font-family: {$shcreate['menu-font']['font-family']};
	font-weight: {$shcreate['menu-font']['font-weight']};	
}

/* Navigation & Dropdown menu */
.nav .open > a, .nav .open > a:hover, 
.nav .open > a:focus {
	background-color: {$shcreate['dropdown-highlight-color']};
	color: {$shcreate['dropdown-font-color']};
}
	
.dropdown-menu {
	background-color: {$shcreate['dropdown-bg-color']['rgba']};
	color: {$shcreate['dropdown-font-color']};
}

.dropdown-menu .new-column-title {
	color: {$shcreate['dropdown-font-color']};
	font-weight: bolder;
}

.dropdown-menu > li a:hover,
.dropdown-menu > li > a,
.dropdown-menu > .active > a:hover {
	color: {$shcreate['dropdown-font-color']};
}

.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus,
.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus {
	color: {$shcreate['dropdown-font-color']};
	background-color: {$shcreate['dropdown-highlight-color']};
}	

.navbar-default .navbar-nav .open .dropdown-menu > .active > a, 
.navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover, 
.navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus {
	background-color: {$shcreate['dropdown-bg-color']['rgba']};
    color: {$shcreate['dropdown-font-color']};
}

.dropdown-submenu>a:after {
    border-left-color: rgba(0,0,0,0.5);
}

.new-column:not(:last-child) {
    border-right: 1px solid $font_color_light;
}

.navbar-default .navbar-nav > .open > a, 
.navbar-default .navbar-nav > .open > a:hover, 
.navbar-default .navbar-nav > .open > a:focus {
	color: $accent_color;
}

.navbar-default .navbar-nav > li > a,
.navbar-default .top-minor-menu > li > a,
.navbar-default .nav-menu-secondary > li > a {
    color: {$shcreate['menu-font-color']};
}

.navbar-default .navbar-toggle .icon-bar {
	background-color: {$shcreate['menu-font-color']};
}

.navbar-default .navbar-nav > li > a:hover,
.navbar-default .top-minor-menu > li > a:hover,
.navbar-default .nav-menu-secondary > li > a:hover {
    color: $accent_color;
}

.navbar-default .navbar-nav > .active > a,
.navbar-default .navbar-nav > .active > a:hover,
.navbar-default .navbar-nav > .active > a:focus,
.navbar-default .navbar-nav > .current-menu-parent > a,
.navbar-default .navbar-nav > .current-menu-ancestor > a {
	background: none;
	color: $accent_color;
}

.navbar-default .navbar-nav > li > a:hover,
.navbar-default .navbar-nav > li > a:focus {
	background: none;
	color: $accent_color;
}

.navbar-nav>li.menu-item:hover >a,
.navbar-nav>li.menu-item:focus >a {
	color: $accent_color;
}

.navbar-nav>li.menu-item:hover,
.navbar-nav>li.menu-item.active:not(.cart-list),
.navbar-nav>li.menu-item:focus,
.navbar-nav>li.menu-item.current-menu-ancestor {
	border-top: 4px solid $accent_color;
	margin-top: -4px;
}

$centered_primary

@media (max-width: 991px) {
	.navbar-default .navbar-nav .open .dropdown-menu > li > a:hover,
	.navbar-default .navbar-nav .open .dropdown-menu > li > a:focus {
		color: $accent_color;
		background-color: {$shcreate['dropdown-bg-color']['rgba']};
	}

	.navbar-default .navbar-nav .open .dropdown-menu > li > a {
		color: {$shcreate['menu-font-color']};
	}
}

$top_sticky_css

h1 {
	$this->h1_css
}
h2 {
	$this->h2_css
}
h3 {
	$this->h3_css
}
h4 {
	$this->h4_css
}
h5 {
	$this->h5_css
}
h6 {
	$this->h6_css
}

.border-bottom {border-bottom: 1px solid $accent_color; padding-bottom: 5px;}

/* footer styles */
.footer-holder { $footer_bg }
.footer, .footer-centered {
	color: {$shcreate['footer-font-color']};
}

$footer_font_fam

$footer_links

/* Above Navigation  & Top Slide */
$slidedown_font_fam
$abovenav_font_fam
.above-nav { 
	color: {$shcreate['above-color']};
	background-color: {$shcreate['above-bg-color']};
	$above_border
}
.above-nav, .no-above {
	$above_offset
}
.above-nav a {
	color: {$shcreate['above-link-color']};
}

.above-nav .above-social li i {
	color: {$shcreate['above-link-color']};
}

.above-social li a:hover i {
	color: $accent_color;
	}

/* End Above Navigation */
.navbar-bg-col { 
	$menu_bg; 
	border-top: 1px solid $font_color_light;
	border-bottom: 1px solid $font_color_light;
}

ul.offcanvas-list li:hover a,
ul.offcanvas-list li.current-menu-item a {
	box-shadow: -4px 0px 0px 0px $accent_color;
	color: $accent_color;
}

.title-section {
	$breadcrumb_bg
	color: $breadcrumb_font;
}

.title-section h1 {
	color: $breadcrumb_font;
} 

.title-section a, .title-section a:visited, .title-section a:active {
	color: $breadcrumb_link;
}
.title-section a:hover {
	color: $accent_color;
}

.entry-meta {
   	border-top: 1px solid $font_color_light;
   	border-bottom: 1px solid $font_color_light;
}
/* Login Widget */
ul.tabs_login li.active_login a {
	color: $accent_color;
}

/* Like Buttons */
a.jm-post-like.liked {
	color: $accent_color;
}

a.jm-post-like:hover,
a.jm-post-like:active,
a.jm-post-like:focus,
a.liked:hover,
a.liked:active,
a.liked:focus { /* default on hover */
   	color: $font_color;
}

#top-slide {
	background-color: {$shcreate['top-slider-bg']};
	color: {$shcreate['top-slider-font']};
}
#top-slide a {
	color: {$shcreate['top-slider-link']};
}

#top-slide h1, #top-slide h2, #top-slide h3, #top-slide h4, #top-slide h4, #top-slide h5, #top-slide h6 {
	color: {$shcreate['top-slider-header']};
}

a.top-slide-control {
	border-right-color: {$shcreate['top-slider-bg']};
}

.footer-widget-holder { $footerw_bg; }
.footer-widget-holder { color: $footerw_font; }
.footer-widget h1, .footer-widget h2, .footer-widget h3, .footer-widget h4, .footer-widget h5, .footer-widget h6 { 
	color: {$shcreate['footerw-header-color']};
}

$footerw_font_fam

.footer-widget ul:not(.sh_adswidget_ul):not(.no-bullet) li a {
	border-bottom: 1px solid $footerw_font_light;
}

.footer-widget.widget_shtab ul li a {
	border-bottom: 0;
}

.footer-widget a, .footer-widget a:visited {
	color: $footerw_font;
}
.footer-widget a:hover {
	color: $accent_color;
}

.footer-widget .tagcloud a {
	color: $footerw_font;
	border: 1px solid $footerw_font;
}

.footer-widget .like-left i.behind {
	color: $footerw_font;
}


#wp-calendar tbody { border: 1px solid $font_color_light; }

.accent, .opened { color: $accent_color !important; }

/* Shortcodes */
.sh-dropcap.theme.foreground { color: $accent_color; }
.sh-dropcap.theme.background { background: $accent_color; color: #fff }

.sh-highlight.foreground.accentcolor {color:$accent_color; }
.sh-highlight.background.accentcolor {background:$accent_color; color:#fff;}

.section-title.boxed { border: 2px solid $font_color; }

.sh-popover {
	color: $accent_color;
}

.imageframe-overlay {
	background: $accent_color;
}

ul.sh-checklist.background i,
ol.sh-checklist.background li:before {
	color: #fff;
	background-color: $accent_color;
}

.social-networks li a:hover { color: {$shcreate['color-accent']}; }
.social-icon a:hover { color: {$shcreate['color-accent']}; }
blockquote { border-left: 5px solid $accent_color; }
a,
a:visited,
a:active { color: {$shcreate['link-color']}; }
a:hover, a:focus { color: $accent_color; }

a.upToTop:active, a.upToTop:hover {
	color: $accent_color;
}

.wpb_tabs li.ui-state-default.ui-tabs-active { box-shadow: inset 0 3px 0px 0px $accent_color; }
.wpb_tour .wpb_tabs_nav li.ui-state-default.ui-tabs-active { border-top: 3px solid $accent_color; }
.wpb_content_element.wpb_tabs .wpb_tour_tabs_wrapper.ui-tabs > ul > li.ui-state-active {
	    border-bottom: 1px solid $page_bg;
}

.page_nav a {
	background: $accent_color;
}

.prev_next {
	border-top: 1px solid $font_color_light;
	border-bottom: 1px solid $font_color_light;
}

.page_nav .current {
	background: $accent_color_light;
}

.page_nav a:hover {
	background: $accent_color_light;
}

input, .contact-mesg, #comment {
	color: $font_color;
}

.sidebar-section { border-bottom: 1px solid $font_color_light; }
$sidebar_font_fam

.like-left { color: $accent_color; }

.section-bar {
	background-color: $font_color;
}

.symbol-title {
	color: $accent_color;
}

button, #submit, .sh-btn, .sh-format .vc_read_more {
	background: $accent_color;
	color: #fff;
}

.sh-btn:hover, .sh-btn:visited, .sh-btn:active {
	color: #fff;
}

.sh-btn-alt:hover, .sh-btn-alt:visited, .sh-btn-alt:active {
	color: #fff;
}

.vc_read_more:visited {
	color: $font_color;
}

.vc_read_more:hover, .vc_read_more:active {
	color: #fff;
}

button:hover, .sh-btn:hover, #submit:hover, .vc_read_more:hover {
	background: $accent_color_light;
}

.tagcloud a { 
	color: #fff; 
}
.tagcloud a:hover {
	background-color: $accent_color;
	color: #fff;
}

/* Portfolios */

.portfolio-item .port-zoom {
	color: #fff;
	background-color: $accent_color;
}

.portfolio-item .port-link {
	color: #fff;
	background-color: $accent_color;
}

.portfolio-item .port-desc {
	border-top: 1px solid $font_color_light;
	border-bottom: 1px solid $font_color_light;
}

.portfolio-item2  .port-holder {
	background: -webkit-linear-gradient(0deg, $accent_color, $accent_color_light 80%) no-repeat;
   	background: linear-gradient(0deg, $accent_color, $accent_color_light 80%) no-repeat;
}

.port-cats {
	border-top: 1px solid $font_color_light;
	border-bottom: 1px solid $font_color_light;
}

.port-cats ul li a:hover,
.port-cats ul li a:active {
	color: $accent_color;
}

.port-cats ul li a.active {
	color: $accent_color;
}

.timeline-badge {
	background-color: $accent_color;
}

/* Woo Commerce */
.navbar-default .navbar-nav > .active > a.top-cart:hover,
.navbar-default .navbar-nav > .active > a.top-cart {
	color: {$shcreate['menu-font-color']};
	background: none;
}

.top-cart-amount {
	color: #ffffff;
	background-color: $accent_color;
}

/* buttons */
.woocommerce-page div.product form.cart .button, 
.woocommerce-page #content div.product form.cart .button,
.woocommerce-page input.checkout-button.wc-forward,
.woocommerce-page input#place_order, 
.woocommerce-page #respond .form-submit input#submit {
	background: $accent_color;
	color: #fff;
	border: 1px solid $accent_color;
} 

.woocommerce-page div.product form.cart .button:hover, 
.woocommerce-page #content div.product form.cart .button:hover, 
.woocommerce-page input.checkout-button.wc-forward:hover,
.woocommerce-page input#place_order:hover,
.woocommerce-page #respond .form-submit input#submit:hover {
	background: $accent_color_light;
	color: #fff;
	border: 1px solid $accent_color;
}

/* messages */
.star-rating span {
	color: $accent_color;
}

.star-rating:hover span {
	color: $accent_color_light;
}

.woo-added-notice-inner { background: $accent_color_light; }

.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
   	background-color: $page_bg;
}
.woocommerce div.product .woocommerce-tabs ul.tabs li.active::before {
	box-shadow: 2px 2px 0px $page_bg;
}

.woocommerce .widget_price_filter .ui-slider .ui-slider-range, 
.woocommerce-page .widget_price_filter .ui-slider .ui-slider-range {
	background-image:none;
	background-color: $accent_color_light !important;
}

.woocommerce .widget_price_filter .ui-slider .ui-slider-handle, 
.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle {
	background: $accent_color !important;
	border: none;
}

.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content, 
.woocommerce-page .widget_price_filter .price_slider_wrapper .ui-widget-content {
	background: $font_color_light !important;
}

.woocommerce-pagination .page-numbers a {
	background: $accent_color;
}

.woocommerce nav.woocommerce-pagination ul li span.current, 
.woocommerce nav.woocommerce-pagination ul li a:hover, 
.woocommerce nav.woocommerce-pagination ul li a:focus, 
.woocommerce #content nav.woocommerce-pagination ul li span.current, 
.woocommerce #content nav.woocommerce-pagination ul li a:hover, 
.woocommerce #content nav.woocommerce-pagination ul li a:focus, 
.woocommerce-page nav.woocommerce-pagination ul li span.current, 
.woocommerce-page nav.woocommerce-pagination ul li a:hover, 
.woocommerce-page nav.woocommerce-pagination ul li a:focus, 
.woocommerce-page #content nav.woocommerce-pagination ul li span.current, 
.woocommerce-page #content nav.woocommerce-pagination ul li a:hover, 
.woocommerce-page #content nav.woocommerce-pagination ul li a:focus {
	background: $accent_color_light;
	color: #fff;
}

.woocommerce-pagination .page-numbers a:hover {
	background: $accent_color_light;
}

EOT;
			// the actual output with header
			header('Content-type: text/css');
			echo $css;

		}  // end construct

		/*
		 * Google Font Style output function (slightly modified from Redux)
		 */
    	public function output($font) {
      		global $wp_styles;
      		if ( !empty( $font['font-family'] ) && !empty( $font['font-backup'] ) ) {
        		$font['font-family'] = str_replace( ', '.$font['font-backup'], '', $font['font-family'] );
      		}

        	$style = '';
        	if (!empty($font)) {
           		foreach( $font as $key=>$value) {
               		if ($key == 'font-options') {
                   		continue;
               		}
           			if (empty($value) && in_array($key, array('font-weight', 'font-style'))) {
               			$value = "normal";
           			}
           			if ( $key == "google" || $key == "subsets" || $key == "font-backup" || empty( $value ) ) {
               			continue;
           			}
           			$style .= $key.':'.$value.';';
           		}
        	}

        	if ( !empty( $style ) ) {
          		if ( !empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
           			$keys = implode(",", $this->field['output']);
           			$this->parent->outputCSS .= $keys . "{" . $style . '}';
         		}

          		if ( !empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
           			$keys = implode(",", $this->field['compiler']);
           			$this->parent->compilerCSS .= $keys . "{" . $style . '}';
          		}
        	}
			return $style;
		}

		/* 
		 * Convert hexdec color string to rgb(a) string 
		 */
		public function hex2rgba($color, $opacity = false) {
			$default = 'rgb(0,0,0)';

			//Return default if no color provided
			if(empty($color))
          	return $default; 

			//Sanitize $color if "#" is provided 
        	if ($color[0] == '#' ) {
        		$color = substr( $color, 1 );
        	}

        	//Check if color has 6 or 3 characters and get values
        	if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        	} elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        	} else {
                return $default;
        	}

        	//Convert hexadec to rgb
        	$rgb = array_map('hexdec', $hex);

        	//Check if opacity is set(rgba or rgb)
        	if($opacity){
        		if(abs($opacity) > 1) {
        			$opacity = 1.0;
				}
        		$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        	} else {
        		$output = 'rgb('.implode(",",$rgb).')';
        	}

        	//Return rgb(a) color string
        	return $output;
		}

		public function colorBrightness($hex, $percent) {
        	// Work out if hash given
        	$hash = '';
        	if (stristr($hex,'#')) {
            	$hex = str_replace('#','',$hex);
            	$hash = '#';
        	}
        	/// HEX TO RGB
        	$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
        	//// CALCULATE 
        	for ($i=0; $i<3; $i++) {
            	// See if brighter or darker
            	if ($percent > 0) {
                	// Lighter
                	$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
            	} else {
                	// Darker
                	$positivePercent = $percent - ($percent*2);
                	$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
            	}
            	// In case rounding up causes us to go to 256
            	if ($rgb[$i] > 255) {
                	$rgb[$i] = 255;
            	}
        	}
        	//// RBG to Hex
        	$hex = '';
        	for($i=0; $i < 3; $i++) {
            	// Convert the decimal digit to hex
            	$hexDigit = dechex($rgb[$i]);
            	// Add a leading zero if necessary
            	if(strlen($hexDigit) == 1) {
                	$hexDigit = "0" . $hexDigit;
            	}
            	// Append to the hex string
            	$hex .= $hexDigit;
        	}
        	return $hash.$hex;
    	}

	} // end class

	new dynamic_style();	
?>
