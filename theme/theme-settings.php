<?php

class ThemeSettings {
	// Properties 
	public $config = array();
	public $post_id = '';  


	// Methods
	public function __construct() {
		$this->config['theme_name']    = 'shcreate';
		$this->config['text_domain']   = 'shcreate';
		$this->config['content_width'] = 604;

		// enqueue scripts for frontend
		add_action( 'wp_enqueue_scripts', array($this, 'shcreate_scripts') );

		if (!is_admin()) {   // only on front end
			// Search filter, only search posts
			add_filter('pre_get_posts',array($this, 'search_filter') );

			add_action('the_content_more_link', array($this, 'new_content_more'), 10, 2 );

			// Template redirect for front page blog templates and default template
			add_action( 'template_include', array($this, 'redirect_to_other_template'));
		}

		// Translation domain load
		add_action('after_setup_theme', array($this, 'load_credence_trans') );
		// WP 4.4 title tag (wp_title replacement)
		add_action('after_setup_theme', array($this, 'credence_slug_setup') );
	}

	/*
	 * Theme Slug Setup (wp_title replacement) wp4.4+
	 */
	public function credence_slug_setup() {
		add_theme_support( 'title-tag' );
	}

	/*
	 * Load the theme text domain
	 */
	public function load_credence_trans() {
		load_theme_textdomain('shcreate', get_template_directory() . '/languages');
	}

	/*
	 * Redirect blog pages set as the front page to their corresponding templates instead of front-page.php
	 * - We'll probably rework this functionality 
	 */
	public function redirect_to_other_template($template) {
		$page_template = basename(get_page_template());
		if (is_front_page()) {
			if ($page_template == 'single-blog.php'
				|| $page_template == 'single-bloggrid.php'
				|| $page_template == 'single-blogtime.php'
				|| $page_template == 'single-portfolio.php'
				|| $page_template == 'single-portfolio2.php'
			) {
				// get the blog template instead
				return get_page_template();
			} elseif ($page_template == 'page.php') { // default template needs sidebar if front page
				if (function_exists( 'is_woocommerce' ) && is_woocommerce()) {
					// don't change it for the shop
				} else {
					return get_page_template();
				}
			}
			return $template;
		}
		return $template;
	}

	/*
	 * Featured Video Check (Our version)
	 */
	public function has_featured_video($pid = null){
    	$id = get_post_meta($pid, 'video_id', true);
    	if($id == null || empty($id)){
        	return false;
    	}else{
        	return true;
    	}
	}

	/*
	 * Featured Video link conversion - for fancybox
	 */
	public function get_featured_video_link($postID) {
		$video_type = get_post_meta($postID, 'video_type', true);
		$video_id = get_post_meta($postID, 'video_id', true);
		$video_id = trim($video_id);
		if ($video_type == 'youtube') {
			if (is_ssl()) {
                $url = 'https://youtu.be/' . $video_id;
            } else {
                $url = 'http://youtu.be/' . $video_id;
            }
		} elseif ($video_type == 'vimeo') {
			if (is_ssl()) {
                $url = 'https://vimeo.com/' . $video_id;
            } else {
                $url = 'http://vimeo.com/' . $video_id;
            }
		} else {
			$url = $video_id;
		}
		return $url;
	}

	/*
	 * Featured Video Output (Our version)
	 */
	public function the_featured_video($postID, $height = '340', $width= '600', $fullscreen = true) {
		$video_type = get_post_meta($postID, 'video_type', true);
		$video_id = get_post_meta($postID, 'video_id', true);
		$video_poster = get_post_meta($postID, 'video_poster', true);
		$wmode = 'transparent';
		$length = 12;
		$random = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

		$proto = 'http';
        if (is_ssl()) {
            $proto = 'https';
        }

    	if($video_type == 'vimeo'){
            $fs = $this->full_screen_mode($fullscreen, true);
            $output = '<div class="elastic-video"><iframe src="' . $proto . '://player.vimeo.com/video/'.$video_id.'" width="'
                .$width.'" height="'.$height.'" frameborder="0" '.$fs.' name="' . $random . '"></iframe></div>';
        }elseif($video_type == 'youtube'){
            $height = '340';  // adjustment for youtube video sizes not fitting (we call 360 height for vimeo)
            $fs = $this->full_screen_mode($fullscreen);
            $output = '<div class="elastic-video"><iframe width="'
                .$width.'" height="'.$height.'" src="' . $proto . '://www.youtube.com/embed/'.$video_id.'?wmode='
                .$wmode.'" frameborder="0" '.$fs.' name="' . $random . '"></iframe></div>';
        } else { // self hosted
			$height = '340';  // adjustment for youtube video sizes not fitting (we call 360 height for vimeo)
            $fs = $this->full_screen_mode($fullscreen);
            $output = '<div class="elastic-video">'
				. '<div class="video-container" style="height:' . $height . ';width:' . $width . ';">'
				. '<video width="100%" height="100%" src="' . $video_id. '" controls poster="' . $video_poster . '"></video>'
				. '</div></div>';
		}

        return $output;
	}

	/*
	 * Featured Video Full Screen Mode
	 */
    public function full_screen_mode($allow, $isvimeo = false){
        if($allow == true){
            if($isvimeo == 'false'){
                return 'webkitAllowFullScreen mozallowfullscreen allowFullScreen';
            }else{
                return 'allowfullscreen';
            }
        }
        return false;
    }

	/*
	 * Enqueue scripts for theme
	 */
	public function shcreate_scripts() {
		global $shcreate;
		global $wp_styles;
		wp_enqueue_script("jquery");

    	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        	wp_enqueue_script( 'comment-reply' );
		}
		// jquery ui elements

    	// Bootstrap 3
    	wp_enqueue_script( 'bootstrap', get_template_directory_uri()
        	. '/js/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '3.2.0', true );

		// isotope for grid layouts and pages that need it
		if (is_page_template('single/single-bloggrid.php')
			|| is_page_template('single/single-portfolio.php')
			|| is_page_template('single/single-portfolio2.php')
			|| is_page_template('single/single-portfolio3.php') 
			) {
			wp_enqueue_script('isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', false, null, true);
			wp_enqueue_script('imagesLoaded', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', false, null, true);
			wp_enqueue_style('isotope-style', get_template_directory_uri() . '/css/isotope-style.css', '');
		}

		// Load more posts ajax for timeline
        if (is_page_template('single/single-blogtime.php')) {
            wp_enqueue_script('ajax-load-posts', get_template_directory_uri() . '/js/load-posts.js',
                array( 'jquery'), null, true );
		}

		// portfolios and blog with fancybox
		if (is_page_template('single/single-portfolio.php')
			|| is_page_template('single/single-portfolio2.php')
			|| is_page_template('single/single-portfolio3.php') 
			|| is_page_template('single/single-blog.php')
			|| is_page_template('single/single-bloggrid.php')
			|| is_page_template('single/single-blogtime.php')
		){
			wp_enqueue_script('fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.min.js', array( 'jquery'), '2.1.5', true );
			wp_enqueue_script('fancybox-media', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-media.min.js', array( 'fancybox'), null, true );
			wp_enqueue_style('fancybox-style', get_template_directory_uri() . '/css/jquery.fancybox.css', '');
		}

		// placeholder shim
		wp_enqueue_script( 'placeholder', get_template_directory_uri()
			. '/js/jquery.placeholder.js', array( 'jquery' ), '', true );

		// register waypoints
        wp_enqueue_script( 'waypoints', get_template_directory_uri(). '/js/waypoints.min.js', array('jquery'), false, true );
		wp_enqueue_script( 'waypoints-sticky', get_template_directory_uri(). '/js/waypoints-sticky.min.js', array('jquery'), false, true );

		//register retina
		if ($shcreate['retina-support'] == 1) {
			//if (!is_woocommerce()) {   // We'll leave it on right now since users must upload a @2x image anyways 
			// To use this functionality
				wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array(), false, true );
			//}
		}

		// register jquery fitvids
		wp_enqueue_script( 'fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', array('jquery'), false, true );

    	// Main Javascript for the theme 
		// Added javascript localization of variables for correct implementation in v2.0
		if (is_page_template()) {
            $sh_template = basename ( get_post_meta( get_the_id(), '_wp_page_template', true ) );
        } else {
            $sh_template = '';
        }
        // top sticky enable navmenu
        if (isset($shcreate['top-sticky']) && $shcreate['top-sticky'] == 'no') {
            $topsticky = 'no';
        } else {
            $topsticky = 'yes';
        }
        // Single page template smooth scroll enable
        if (isset($shcreate['single_page_template']) && $shcreate['single_page_template'] == '1') {
            $smooth_scroll = 'yes';
        } else {
            $smooth_scroll = 'no';
        }
        // Chrome smooth scrolling enable
        if (isset($shcreate['chrome-smooth']) && $shcreate['chrome-smooth'] == '1') {
            $chrome_smooth = 'yes';
        } else {
            $chrome_smooth = 'no';
        }
        // Site fade in effect
        if (isset($shcreate['site-fadein']) && $shcreate['site-fadein'] == '1') {
            $site_fadein = 'yes';
        } else {
            $site_fadein = 'no';
        }
		// Return to top
		if (isset($shcreate['totop']) && $shcreate['totop'] == '0') {
			$to_top = 'no';
		} else {
			$to_top = 'yes';
		}
		// Side Menu set
		if (isset($shcreate['top-side-menu']) && $shcreate['top-side-menu'] == '2') {
			$side_menu = 'yes';
		} else {
			$side_menu = 'no';
		}
    	wp_enqueue_script( 'main-script', get_template_directory_uri() . '/js/main.min.js', array( 'jquery' ), '', true);
		wp_localize_script(  'main-script', 'credence_globals', array(
			'topsticky'       => $topsticky,
			'sh_smoothScroll' => $smooth_scroll,
			// 'chrome_smooth'   => $chrome_smooth, //depricated
			'site_fadein'     => $site_fadein,
			'side_menu'       => $side_menu,
			'totop'           => $to_top
        ));

		// Font awesome
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome/css/font-awesome.min.css', '');

		// linearicons
		if (isset($shcreate['linear-icons']) && $shcreate['linear-icons'] ) { 
			wp_enqueue_style( 'linearicons', get_template_directory_uri() . '/css/linear-icons/style.css', '');
		} 
		// ionicons
		if (isset($shcreate['ion-icons']) && $shcreate['ion-icons'] ) {
			wp_enqueue_style('ionicons', get_template_directory_uri() . '/css/ionicons/css/ionicons.min.css', '');
		}

		// Bootstrap style
		wp_enqueue_style( 'bootstrap-style', get_template_directory_uri() . '/js/bootstrap/css/bootstrap.min.css' );

		// Theme Styles
		$theme = wp_get_theme();
		wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/css/styles.min.css', 
				array ('bootstrap-style'), $theme->version);

		// Theme Dynamic Styles - action in theme init because of wp_ajax calls 
        $dyn_url = add_query_arg( 'action', 'shdynamic_css', admin_url('admin-ajax.php'));
        wp_enqueue_style( 'dynamic-style', $dyn_url, array('theme-style'), $theme->version);

		/* Woocommerce pages stylesheet, load for woocommerce only 
 		 * Check if WooCommerce is installed and active first
 		 */
		if (function_exists( 'is_woocommerce' )) { 
			if (is_woocommerce()
				|| is_cart()
				|| is_checkout()
				|| is_account_page()
			) {
				wp_enqueue_style( 'woo-style', get_template_directory_uri() . '/css/woocommerce.css', array('theme-style'), null);
			}
		}

	 	// bxslider	
		wp_enqueue_style( 'bxslider-css', get_template_directory_uri() . '/js/bxslider/jquery.bxslider.css');
        wp_enqueue_script( 'bxslider-js', get_template_directory_uri() . '/js/bxslider/jquery.bxslider.min.js', array('jquery'), null, true);

		// style switcher for demos only
		if (isset($shcreate['style-switcher']) && $shcreate['style-switcher'] == '1') {
			wp_register_script( 'style-switch',
					get_template_directory_uri() . '/js/styleswitch.js', array('main-script'), '', true);
			wp_enqueue_script( 'style-switch' );
			wp_localize_script(  'style-switch', 'switcher_globals', array(
                'sh_templateUri'  => get_template_directory_uri(),
            ));
			wp_register_script( 'theme-switch', 
					get_template_directory_uri() . '/js/style-switcher.js', array ('main-script'), '', true);
			wp_enqueue_script( 'theme-switch' );
			wp_register_style( 'style-switcher-style', get_template_directory_uri() . '/css/style-switcher.css');
			wp_enqueue_style( 'style-switcher-style' );
			// alternate stylesheets
			wp_enqueue_style( 'demo-emerald', get_template_directory_uri() . '/css/demo/emerald-theme.css', array(), '1.0');
			$wp_styles->add_data('demo-emerald', 'alt', true);
			$wp_styles->add_data( 'demo-emerald', 'title', __( 'emerald-theme', 'shcreate') );
			wp_enqueue_style( 'demo-alizarin', get_template_directory_uri() . '/css/demo/alizarin-theme.css', array(), '1.0');
			$wp_styles->add_data('demo-alizarin', 'alt', true);
			$wp_styles->add_data( 'demo-alizarin', 'title', __( 'alizarin-theme', 'shcreate') );
			wp_enqueue_style( 'demo-amethyst', get_template_directory_uri() . '/css/demo/amethyst-theme.css', array(), '1.0');
			$wp_styles->add_data('demo-amethyst', 'alt', true);
			$wp_styles->add_data( 'demo-amethyst', 'title', __( 'amethyst-theme', 'shcreate') );
			wp_enqueue_style( 'demo-blue', get_template_directory_uri() . '/css/demo/blue-theme.css', array(), '1.0');
			$wp_styles->add_data('demo-blue', 'alt', true);
			$wp_styles->add_data( 'demo-blue', 'title', __( 'blue-theme', 'shcreate') );
			wp_enqueue_style( 'demo-carrot', get_template_directory_uri() . '/css/demo/carrot-theme.css', array(), '1.0');
            $wp_styles->add_data('demo-carrot', 'alt', true);
			$wp_styles->add_data( 'demo-carrot', 'title', __( 'carrot-theme', 'shcreate') );
			wp_enqueue_style( 'demo-silver', get_template_directory_uri() . '/css/demo/silver-theme.css', array(), '1.0');
            $wp_styles->add_data('demo-silver', 'alt', true);
			$wp_styles->add_data( 'demo-silver', 'title', __( 'silver-theme', 'shcreate') );
			wp_enqueue_style( 'demo-sunflower', get_template_directory_uri() . '/css/demo/sunflower-theme.css', array(), '1.0');
            $wp_styles->add_data('demo-sunflower', 'alt', true);
			$wp_styles->add_data( 'demo-sunflower', 'title', __( 'sunflower-theme', 'shcreate') );
			wp_enqueue_style( 'demo-wetasphalt', get_template_directory_uri() . '/css/demo/wetasphalt-theme.css', array(), '1.0');
            $wp_styles->add_data('demo-wetasphalt', 'alt', true);
			$wp_styles->add_data( 'demo-wetasphalt', 'title', __( 'wetasphalt-theme', 'shcreate') );
		}

		// Smooth scroll for webkit browsers
		// Chrome smooth scrolling enable - depricated
		/*
        if (isset($shcreate['chrome-smooth']) && $shcreate['chrome-smooth'] == '1') {
            $chrome_smooth = '1';
        } else {
            $chrome_smooth = '2';
        }
		if ($chrome_smooth == 1) {
			wp_enqueue_script( 'mousewheel', get_template_directory_uri() 
			. '/js/jquery.mousewheel.min.js', array('jquery'), '3.1.12', true);
			wp_enqueue_script( 'smoothscroll', get_template_directory_uri() 
			. '/js/jquery.simplr.smoothscroll.min.js', array('jquery'), '1.1', true);
		}
		*/
	}

	/*
	 * Filter searches to just posts
	 */
	public function search_filter($query) {
		global $shcreate;
		if (!isset($shcreate['menu-search-content']) || $shcreate['menu-search-content'] == 1) {   // search only posts
			if ($query->is_search) {
				$query->set('post_type', 'post');
			}
		}
		return $query;
	}

	public function theme_paging_nav($custom_query=null, $custom_page=null) {
		global $wp_query;
		if ($custom_query == null) {
    		$total_pages = $wp_query->max_num_pages;
		} else {
			$total_pages = $custom_query->max_num_pages;
		}

    	if ($total_pages > 1){
			if (is_front_page()) {  // if a blog template is set as frontpage
				$current_page = get_query_var('page') ? get_query_var('page') : 1;
			} elseif ($custom_query == null) {	
        		$current_page = max(1, get_query_var('paged'));
			} else {
				$current_page = $custom_page;
			}

        	echo '<div class="page_nav">';

        	echo paginate_links(array(
            	//'base' => get_pagenum_link(1) . '%_%',  // Old method base and format do not work with search
            	//'format' => '/page/%#%',
				'base' => esc_url( get_pagenum_link() ) . '%_%', // %_% will be replaced with format below 10	        
				'format' => ( ( get_option( 'permalink_structure' ) // %#% will be replaced with page number
					&& ! $wp_query->is_search ) 
					|| ( is_home() && get_option( 'show_on_front' ) !== 'page' 
					&& ! get_option( 'page_on_front' ) ) ) ? '?paged=%#%' : '&paged=%#%', 
            	'current' => $current_page,
            	'total' => $total_pages,
            	'prev_text' => '<i class="fa fa-angle-left"></i> &nbsp;&nbsp;&nbsp;' . __('Prev', 'shcreate'),
            	'next_text' => __('Next', 'shcreate') . ' &nbsp;&nbsp;&nbsp;<i class="fa fa-angle-right"></i>'
        	));
        	echo '</div>';
    	}
	}

	/**
	* Format the Read More link
	*/
	public function new_content_more($link, $text) {
		return str_replace(
        	'more-link'
        	,'sh-btn'
        	,$link
    	);
	}   

	/**
	*  Grid post meta top layout
	*  - static for now, the default meta is customizable "blog-attributes"
	**/
	public function grid_top_meta() {
		global $shcreate;
		global $post;
	
		echo '<div class="grid-top-meta">';
		// Post author
        if ( 'post' == get_post_type() ) {
            $author = sprintf( '<span class="author vcard">'
                . __('By', 'shcreate') .' <a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                esc_attr( sprintf( __( 'View all posts by %s', 'shcreate' ), get_the_author() ) ),
                get_the_author()
            );
        }	

		echo $author . ' | ';
		// post date
		if ( ! has_post_format( 'link' ) && 'post' == get_post_type() ) {
            $this->theme_entry_date();
        }
		echo '</div>';

	}

    /**
    *  Grid post meta bottom layout
    *  - static for now, the default meta is customizable "blog-attributes"
    **/
    public function grid_bottom_meta() {
        global $shcreate;
        global $post;
        echo '<div class="grid-bottom-meta">';

		// Comments
        echo '<span><a href="' . get_comments_link() . '">' 
			. '<i class="fa fa-comments"></i> '
			. get_comments_number() . '</a></span>';

		// Likes
        echo '<span>' . getPostLikeLink( $post->ID ) . '</span>';

		// Read More
        echo '<span class="bottom-read"><a href="' . get_the_permalink() 
		   . '">' . __('Read more', 'shcreate') . ' &nbsp;<i class="fa fa-angle-right"></i></a></span>';

        echo '</div>';

    }

	/**
 	* Print HTML with meta information for current post: categories, tags, permalink, author, and date.
	*
	* Blog attributes from theme options ($shcreate['blog-attributes']) 
	*
	* '1' => 'Posted By',
    * '2' => 'Date',
    * '3' => 'Category',
    * '4' => 'Tags',
    * '5' => 'Comments',
    * '6' => 'Likes'
 	*/
	public function theme_entry_meta() {
		global $shcreate;
		global $post;
		$attributes = $shcreate['blog-attributes'];
		$count = count($attributes);
		$i = 1;
		foreach ($attributes as $key => $val) {
			if ($val == 1) { // Posted By
				// Post author
            	printf( '<span class="author vcard"> '
                   	. __('By', 'shcreate') .' <a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
               		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
               		esc_attr( sprintf( __( 'View all posts by %s', 'shcreate' ), get_the_author() ) ),
               		get_the_author()
            	);
			} elseif ($val == 2) { // Date
				if ( ! has_post_format( 'link' ) ) {
            		$this->theme_entry_date();
        		}
			} elseif ($val == 3) { // Category
				// Translators: used between list items, there is a space after the comma.
        		$categories_list = get_the_category_list( __( ', ', 'shcreate' ) );
        		if ( $categories_list ) {
            		echo '<span class="categories">'
                 	. $categories_list . '</span>';
        		}
			} elseif ($val == 4) { // Tags
				// Translators: used between list items, there is a space after the comma.
        		$tag_list = get_the_tag_list( '', __( ', ', 'shcreate' ) );
        		if ( $tag_list ) {
            		echo '<span class="tags-links"> <i class="fa fa-tags"></i> ' . $tag_list . '</span>';
        		}
			} elseif ($val == 5) { // Comment Count
				if ( get_comments_number() > 1 || get_comments_number() == 0 ) {
            		$comment = __('Comments', 'shcreate');
        		} else {
            		$comment = __('Comment', 'shcreate');
       		 	}
        		echo '<span><a href="' . get_comments_link() . '">' . get_comments_number() . ' ' . $comment . '</a></span>';
			} elseif ($val == 6) { // Likes
				if (get_post_type($post) == 'post') {   // only show likes for posts (not pages)
					echo '<span>' . getPostLikeLink( $post->ID ) . '</span>';
				} 

			}

			// skip bars for last entry, empty tag lists, and empty / custom categories (for custom posts)
			if ($i == $count 
			|| ($val == 4  && !$tag_list) 
			|| ($val == 3 && !$categories_list) 
			) { 
				echo '';
			} else {
				echo ' | ';
			}
			$i++;
		}

	}

	/**
 	* Print HTML with date information for current post.
 	*/
	public function theme_entry_date( $echo = true ) {
    	if ( has_post_format( array( 'chat', 'status' ) ) ) {
        	$format_prefix = __( '%1$s on %2$s', 'shcreate' );
    	} else {
        	$format_prefix = '%2$s';
		}

    	$date = sprintf( '<span><time class="entry-date updated" datetime="%3$s">%4$s</time></span>',
        	esc_url( get_permalink() ),
        	esc_attr( sprintf( __( 'Permalink to %s', 'shcreate' ), the_title_attribute( 'echo=0' ) ) ),
        	esc_attr( get_the_date( 'c' ) ),
        	esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date( get_option( 'date_format')) ) )
    	);

    	if ( $echo ) {
        	echo $date;
		}
    	return $date;
	}

	/**
	 * Custom excerpt for grid layouts
	**/
	public static function custom_excerpt_length( $length=30 ) {
		global $grid_needed;  // shortcodes blog grid smaller excerpts
		if (true == $grid_needed) {  
			$length = 20;
		}
		return $length;
	}

	/**
	 * Custom excerpt length for blog medium with sidebars
	**/
	public static function custom_excerpt_lengthb($length) {
        return 20;
    }

	/**
    * Breadcrumbs for pages
    */
    public function the_breadcrumb() {
        global $post;
		$separator = '<i class="fa fa-angle-right"> </i>';
        echo '<ul class="breadcrumbs">';
        if (!is_home()) {
            echo '<li><a href="';
			echo home_url();
            echo '">';
			echo '<i class="fa fa-home"></i>';
            //echo __('Home', 'shcreate');
            echo '</a></li><li class="separator">' . $separator . ' </li>';
            if (is_category() || is_single()) {
                echo '<li>';
				if (is_singular('people')) {
					// getting the custom taxonomy for people to show in breadcrumbs instead of standard category
					$peoplecat = '';
					//$category = get_the_category();
					$cat = get_the_terms(get_the_ID(), 'peoplecat');
					if ($cat && ! is_wp_error( $cat) ) {
						$cat_array = array_values($cat);
                        $tempcat = array_shift($cat_array);
						$peoplecat = $tempcat->name;
					}
					echo '</li><li> ' . $peoplecat; 
				} else {
                	the_category(' </li><li class="separator">' . $separator . '</li><li> ');
				}

                if (is_single()) {
					if (get_the_category()) {  // if it has a category (not a custom taxonomy) echo the separator first
                    	echo '</li><li class="separator">' . $separator . '</li><li>';
                    	the_title();
                    	echo '</li>';
					} else if (is_singular('people')) {
						echo '</li><li class="separator">' . $separator . '</li><li>';
                        the_title();
                        echo '</li>';
					} else {
						the_title();
						echo '</li>';
					}
                }
            } elseif (is_page()) {
                if($post->post_parent){
                    $anc = get_post_ancestors( $post->ID );
                    $title = get_the_title();
                    foreach ( $anc as $ancestor ) {
                        $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">' . $separator . '</li>';
                    }
                    echo $output;
                    echo '<span title="'.$title.'"> '.$title.'</span>';
                } else {
                    echo '<li><span> '.get_the_title().'</span></li>';
                }
            }
        }

        if (is_tag()) {
            $tagTitle = single_tag_title("", false);
            echo '<li>' . $tagTitle . '</li>';
        }
        elseif (is_day()) {echo '<li>' . __('Archive for', 'shcreate') . the_time('F jS, Y') . '</li>';}
        elseif (is_month()) {echo '<li>' . __('Archive for', 'shcreate') . the_time('F, Y') . '</li>';}
        elseif (is_year()) {echo '<li>' . __('Archive for', 'shcreate') .  the_time('Y') . '</li>';}
        elseif (is_author()) {echo '<li>' . __('Author Archive', 'shcreate') . '</li>';}
        elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo '<li>' . __('Blog Archives', 'shcreate') . '</li>';}
        elseif (is_search()) {echo '<li>' . __('Search Results', 'shcreate') . '</li>';}
        echo '</ul>';
    }


	/*
	 * Return media (image, video, audio) for display above title
	 */
	public function catch_that_media() {
  		global $post, $posts;
  		$first_img = '';
  		ob_start();
  		ob_end_clean();
  		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  		$first_img = $matches [1] [0];

  		if(empty($first_img)){ //Defines a default image
    		$first_img = "";
  		}
  		return $first_img;
	}

	/**
	 * Check for the existence of our menu
	 **/
	public function menu_check() {
		$menu_name = false;
		$all_menus = wp_get_nav_menus();
        // We need the nav-menu location registered for 'Navigation Menu' in theme-settings.php
        $nav_menus = (get_nav_menu_locations());
        $menu_key = 'nav-menu';
        foreach ($nav_menus as $k => $v) {
            if ($k == $menu_key) {
				$menu_id = apply_filters( 'wpml_object_id', $v, 'nav_menu', TRUE );
                foreach ($all_menus as $all => $m) {
                    if ($menu_id == $m->term_id) {
                        $menu_name = $m->name;
                    }
                }
            }
        }
		return $menu_name;
	}

	/**
	 * Check for the existence of the main side menu
	 **/
	public function main_side_check() {
		$menu_name = false;
		$all_menus = wp_get_nav_menus();
		$nav_menus = (get_nav_menu_locations());
        $menu_key = 'main-side-menu';
        foreach ($nav_menus as $k => $v) {
            if ($k == $menu_key) {
				$menu_id = apply_filters( 'wpml_object_id', $v, 'nav_menu', TRUE );
                foreach ($all_menus as $all => $m) {
                    if ($menu_id == $m->term_id) {
                        $menu_name = $m->name;
                    }
                }
            }
        }
        return $menu_name;
    }


	/**
	 * Check for our footer menu existing
	 */
	public function footer_menu_check() {
        $menu_name = false;
        $all_menus = wp_get_nav_menus();
        $nav_menus = (get_nav_menu_locations());
        $menu_key = 'footer-menu';
        foreach ($nav_menus as $k => $v) {
            if ($k == $menu_key) {
				$menu_id = apply_filters( 'wpml_object_id', $v, 'nav_menu', TRUE );
                foreach ($all_menus as $all => $m) {
                    if ($menu_id == $m->term_id) {
                        $menu_name = $m->name;
                    }
                }
            }
        }
        return $menu_name;
	}

	/**
	 * Check for our offcanvas sidebar menu
	 **/
	public function side_menu_check() {
		$menu_name = false;
		$all_menus = wp_get_nav_menus();
		$nav_menus = (get_nav_menu_locations());
		$menu_key = 'side-menu';
		foreach ($nav_menus as $k => $v) {
            if ($k == $menu_key) {
				$menu_id = apply_filters( 'wpml_object_id', $v, 'nav_menu', TRUE );
                foreach ($all_menus as $all => $m) {
                    if ($menu_id == $m->term_id) {
                        $menu_name = $m->name;
                    }
                }
            }
        }
        return $menu_name;
    }

	/**
	  * Display Audio in loop
	 **/
	public function add_audio_player(){
        // get post
        global $post;
        // have post ID?
        if ( !empty( $post->ID ) ) {
        
            // get audio
            $meta_value = get_post_meta( $post->ID, 'enclosure', true);
        
            // have audio?
            if ( !empty( $meta_value ) ) {
				// only get the first audio entry and split on line endings (hopefully that's always the case)
				$audio = explode("\n", $meta_value);
				//print_r($audio);
				$attr = array(
					'src' => trim($audio[0]),
				);
				//print_r($attr);
				return wp_audio_shortcode($attr);
            }
        }

        // return excerpt
        return $audio;
    }       

	/**
	  * Display attachment images as slider gallery
	 **/
	public function add_bxslider() { 
		global $post;
		$attachments = get_children(array(
			'post_parent'    => $post->ID, 
			'order'          => 'ASC', 
			'orderby'        => 'menu_order',  
			'post_type'      => 'attachment', 
			'post_mime_type' => 'image',
			'caption'        => $post->post_excerpt, 
		));

		if ($attachments) { // see if there are images attached to posting 
			echo "\n" . '<!-- Begin Slider -->' . "\n"
			   . '<div class= "post-slides">' . "\n"
			   . '<ul class="bxslider">' . "\n";

			// create the list items for images with captions

			foreach ( $attachments as $attachment_id => $attachment ) {

				echo '<li>';
				//echo wp_get_attachment_image($attachment_id, 'large');
				$imageUrl = wp_get_attachment_image_src($attachment_id, 'large');
				echo '<img class="img-responsive" src="' . $imageUrl[0] . '" />';
				//echo '<p> Test excerpt';
				//echo get_post_field('post_excerpt', $attachment->ID);
				//echo '</p>';
				echo '</li>' . "\n";

			} 

			echo '</ul></div>' . "\n"
			   . '<!-- End Slider -->' . "\n";
		} 
	}

	public function shcreate_the_attached_image() {
	    /**
	     * Filter the image attachment size to use.
	     *
	     * @param array $size {
	     *     @type int The attachment height in pixels.
	     *     @type int The attachment width in pixels.
	     * }
	     */
	    $attachment_size     = apply_filters( 'shcreate_attachment_size', array( 724, 724 ) );
	    $next_attachment_url = wp_get_attachment_url();
	    $post                = get_post();

	    /*
	     * Grab the IDs of all the image attachments in a gallery so we can get the URL
	     * of the next adjacent image in a gallery, or the first image (if we're
	     * looking at the last image in a gallery), or, in a gallery of one, just the
	     * link to that image file.
	     */
	    $attachment_ids = get_posts( array(
	        'post_parent'    => $post->post_parent,
	        'fields'         => 'ids',
	        'numberposts'    => -1,
	        'post_status'    => 'inherit',
	        'post_type'      => 'attachment',
	        'post_mime_type' => 'image',
	        'order'          => 'ASC',
	        'orderby'        => 'menu_order ID'
	    ) );

	    // If there is more than 1 attachment in a gallery...
	    if ( count( $attachment_ids ) > 1 ) {
	        foreach ( $attachment_ids as $attachment_id ) {
            	if ( $attachment_id == $post->ID ) {
                	$next_id = current( $attachment_ids );
                	break;
            	}
        	}

        	// get the URL of the next image attachment...
        	if ( $next_id )
            	$next_attachment_url = get_attachment_link( $next_id );

        	// or get the URL of the first image attachment.
        	else
            	$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
    	}	

    	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
        	esc_url( $next_attachment_url ),
        	the_title_attribute( array( 'echo' => false ) ),
        	wp_get_attachment_image( $post->ID, $attachment_size )
    	);
	}
}

