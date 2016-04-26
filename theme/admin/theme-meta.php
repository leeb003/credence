<?php
/**
 *  Class to create metaboxes for specific pages
 */
class Theme_metabox {
	

	//methods
    public function __construct() {
		// Metaboxes for all Pages (theme overrides)
		add_action("add_meta_boxes", array(&$this, "page_layout_over") );

		// Metaboxes for Featured Video
        add_action("add_meta_boxes", array(&$this, "featured_videos") );

		// Metaboxes for blog
		add_action("add_meta_boxes", array(&$this, "blog_fields"));

		// Metaboxes for blog timeline
		add_action("add_meta_boxes", array(&$this, "blogtime_fields"));

		// Metaboxes for blog grid
		add_action("add_meta_boxes", array(&$this, "bloggrid_fields"));

		// Metaboxes for portfolio
		add_action("add_meta_boxes", array(&$this, "portfolio_fields"));

		// Metaboxes for people
		add_action("add_meta_boxes", array(&$this, "people_fields"));

		// Save the post data
        add_action( 'save_post', array(&$this, 'save_postdata') );
	}

	/*
	 * Featured Videos
	 */
	public function featured_videos() {
		$post_types = get_post_types();
    	foreach ($post_types as $post_type) {
        	$exclude = array ( 'product' );  // array of post types to exclude from allowing videos
        	if ( true === in_array( $post_type, $exclude) ) {
            	// skip
        	} else {
            	// just posts and portfolio items for now
				add_meta_box("featured_videos", __('Featured Video', 'shcreate' ),
            		array(&$this, 'video_custom_box'), 'post', 'side', 'default');
				add_meta_box("featured_videos", __('Featured Video', 'shcreate' ),
            		array(&$this, 'video_custom_box'), 'portfolio_entry', 'side', 'default');
        	}
    	}
	}

	/*
	 * Page Layout override
	 */
	public function page_layout_over() {
		global $post;
		$post_type = get_post_type($post);
		if ($post_type == 'page') {
			add_meta_box("page_layout", __('Theme Layout Overrides', 'shcreate'),
				array(&$this, "layout_custom_box"), "", "side", "default");
		}
	}

	/* 
	 * Blog Options
	*/
	public function blog_fields() {
		add_meta_box("blog_options", __('Blog Options', 'shcreate' ),
            array(&$this, "blog_custom_box"), "", "side", "default");
    }

	/*
	 * Blog Timeline Options
	 */
	public function blogtime_fields() {
		add_meta_box("blogtime_options", __('Blog Timeline Options', 'shcreate'),
			array(&$this, "blogtime_custom_box"), "", "side", "default");
	}

	/* 
	 * Blog Grid Options
	 */
	public function bloggrid_fields() {
		add_meta_box("bloggrid_options", __('Blog Grid Options', 'shcreate' ),
			array(&$this, "bloggrid_custom_box"), "", "side", "default");
	}


	/*
	 * Portfolio options
	 */
	public function portfolio_fields() {
		add_meta_box("portfolio_options", __( 'Portfolio Options', 'shcreate' ),
            array(&$this, "portfolio_custom_box"), "", "side", "default");	

	}

	/*
	 * People Options
	 */
	public function people_fields() {
		add_meta_box("person_settings", __( 'Person Settings', 'shcreate' ),
			array(&$this, "person_settings_custom_box"), "people", "normal", "high");
	}

	/** 
	 * Person Custom Post Type Settings
	 */
	public function person_settings_custom_box() {
		global $post;

		$person_name = get_post_meta( $post->ID, 'person_name', true);
		$person_title = get_post_meta( $post->ID, 'person_title', true);
		$person_link = get_post_meta( $post->ID, 'person_link', true);
		$person_quick = get_post_meta( $post->ID, 'person_quick', true);
		$social_icon = get_post_meta( $post->ID, 'social_icon', true);
		$social_link = get_post_meta( $post->ID, 'social_link', true);

		$selected = 'selected="selected"';

		echo '<div id="person-settings"><table class="meta-table">'
			. '<tr><td>' . __('Enable Link To Full Profile?', 'shcreate') . '</td><td><select name="person_link">'
			. '<option value="enable" ' . ($person_link == 'enable' ? $selected : '') . '>' . __('Enable', 'shcreate') . '</option>'
			. '<option value="disable" ' . ($person_link == 'disable' ? $selected : '') . '>' . __('Disable', 'shcreate') . '</option>'
			. '</select></td></tr>'
			. '<tr><td>' . __('Person Name:', 'shcreate') 
			. '</td><td><input type="text" name="person_name" value="' . $person_name . '" />'
			. '<tr><td>' . __('Person Title:', 'shcreate') . '</td><td><input type="text" name="person_title" value="' 
			. $person_title . '" /></td></tr>';

		echo '<tr><td>' . __('Short Description (for front page)', 'shcreate') . '</td><td></td></tr>'
			. '<tr><td colspan="2"><textarea class="text-wide" name="person_quick">' . $person_quick . '</textarea></td></tr>'; 

		echo '<tr class="social-row"><td>' . __('Social Icons', 'shcreate') 
			. ':</td><td><div class="add-social">' . __('Add An Icon', 'shcreate') . '</div></td></tr>';

        // Add existing social icons
        $i = 0;
        if (isset($social_icon[0])) {
            foreach ($social_icon as $k => $v) {
                echo '<tr class="sorting sort-' . $i . '">'
                  . '<td>' . __('Link', 'shcreate') 
				  . ': <input class="social-link" type="text" name="social_link[]" value="' . $social_link[$i] . '" /></td>'
                  . '<td class="icon-td">' . __('Icon', 'shcreate') 
				  . ': <div class="choose-social"><i class="fa ' . $v . ' fa-2x"></i></div>'
                  . '<input type="hidden" class="social_icon" name="social_icon[]" value="' . $v . '" />'
                  . '<a href="#" class="remove-link">' . __('Remove', 'shcreate') . '</a>'
                  . '<div class="sorter"><i class="fa fa-arrows"></i></div></td></tr>';
                $i++;
            }
        }

		echo '</table></div>';
		
		// Load icons in hidden div for re-use
		$icon_file = dirname(__FILE__) . '/inc/icons.txt';
		$fh = fopen( $icon_file, 'r');
		$icons = array();
		while($line = fgets($fh)) {
    		$icons[] = trim($line);
		}
		fclose($fh);
		$output = '';
        // The icons div container
        $output .= '<div class="choose-icons" title="' . __('Choose An Icon', 'shcreate') . '" style="display:none">'
             . '<table class="icon-list"><tr>';
        $i = 1;
        foreach ($icons as $icon) {
            if ($icon != '') {
                if (preg_match('/^#(.*)/', $icon, $matches)) {  // Header Rows
                    $output .= '</tr><tr><td colspan="20"><strong>' . $matches[1] . '</strong></td></tr>';
                    $i = 1;
                } else {    // Icons
                    $output .= '<td><div class="select-social-icon"><i class="fa ' . $icon . ' fa-2x"></i></div></td>';
                    $i++;
                }
            }
            if ($i >= 10) {
                $output .= '</tr><tr>';
                $i = 1;
            }
        }
        $output .= '</table></div>';
		echo $output;
	}

	/**
	 * Custom meta box for Featured Video
	 **/
	public function video_custom_box() {
		global $post;
		$video_type = get_post_meta( $post->ID, 'video_type', true);
		$video_id = get_post_meta( $post->ID, 'video_id', true);
		$video_poster = get_post_meta( $post->ID, 'video_poster', true);

		$selected = 'selected="selected"';
		echo '<div id="featured-videos"><table class="meta-table">'
           . '<tr><td>' . __( "Video Type: ", "shcreate" ) . '</td>'
           . '</td><td><select id="video-type" name="video-type">'
           . '<option value="youtube" ' . ($video_type == 'youtube' ? $selected : '') . '>' 
           . __('Youtube Video', 'shcreate') . '</option>'
           . '<option value="vimeo" ' . ($video_type == 'vimeo' ? $selected : '') . '>' 
           . __('Vimeo Video', 'shcreate') . '</option>'
			. '<option value="selfhosted" ' . ($video_type == 'selfhosted' ? $selected : '') . '>'  
           . __('Self Hosted Video', 'shcreate') . '</option>'
           . '</select></td><td></td></tr>'

           . '<tr><td>' .  __( "ID or URL: " , "shcreate" ) . '</td>'
           . '<td><input type="text" id="video-id" name="video-id" value="' . $video_id . '">'
           .'</td><td></td></tr>'

		   . '<tr><td>' .  __( "Poster URL: " , "shcreate" ) . '</td>'
           . '<td><input type="text" id="video-poster" name="video-poster" value="' . $video_poster . '">'
           .'</td><td></td></tr>'

			. '<tr><td colspan="2">' 
			. __('Vimeo and Youtube Videos just enter the <b>Video ID</b>.  Enter the url to the video for Self Hosted and add a poster image url if wanted.', 'shcreate') 
			. '</td><td></td></tr></table></div>';
    }

	/**
	 * Custom meta box for theme overrides
	**/
	public function layout_custom_box() {
		global $post;
		$enable_width = get_post_meta( $post->ID, 'enable_width', true);
		$page_width = get_post_meta( $post->ID, 'page_width', true);
		$enable_bread = get_post_meta( $post->ID, 'enable_bread', true);
		$breadcrumbs = get_post_meta( $post->ID, 'breadcrumbs', true);

		$enable_menubar = get_post_meta( $post->ID, 'enable_menubar', true);
		$menubar = get_post_meta( $post->ID, 'menubar', true);
		$enable_menushadow = get_post_meta( $post->ID, 'enable_menushadow', true);
		$menushadow = get_post_meta( $post->ID, 'menushadow', true);

		if ($enable_width == '') { // keep disabled unless they allow
			$enable_width = 'no';
		}
		if ($page_width == '') {
			$page_width = 'wide';
		}
		if ($enable_bread == '') {
			$enable_bread = 'no';
		} 
		if ($breadcrumbs == '') {
			$breadcrumbs = 'show';
		}

		if ($menubar == '') {
			$type_menubar = 'classic';
		}
		if ($menushadow == '') {
			$menushadow = 'hide';
		}
		$selected = 'selected="selected"';
		$width_check = $enable_width == 'yes' ? 'checked="checked"' : ''; 
		$bread_check = $enable_bread == 'yes' ? 'checked="checked"' : '';
		$menubar_check = $enable_menubar == 'yes' ? 'checked="checked"' : '';
		$menushadow_check = $enable_menushadow == 'yes' ? 'checked="checked"' : '';
		
		echo '<div id="page-options-override"><table class="meta-table">'
			. '<tr><td>' . __("Override Theme Width?", "shcreate") . '</td>'
			. '</td><td><input type="checkbox" name="enable_width" value="yes" ' . $width_check . '" />'
			. '</td><td></td></tr>'

			. '<tr><td>' . __("Page Width", "shcreate") . '</td>'
			. '<td><select name="page_width">'
			. '<option value="wide" ' . ($page_width == 'wide' ? $selected : '') . '>'
			. __('Wide', 'shcreate') . '</option>'
			. '<option value="boxed" ' . ($page_width == 'boxed' ? $selected : '') . '>'
			. __('Boxed', 'shcreate') . '</option>'
			. '<option value="boxedoffset" ' . ($page_width == 'boxedoffset' ? $selected : '') . '>'
            . __('Boxed Offset', 'shcreate') . '</option>'
			. '</select></td><td></td></tr>'

			. '<tr><td>' . __("Override Theme Breadcrumb?", "shcreate") . '</td>'
            . '</td><td><input type="checkbox" name="enable_bread" value="yes" ' . $bread_check . '" />'
            . '</td><td></td></tr>'

            . '<tr><td>' . __("Show Breadcrumb bar", "shcreate") . '</td>'
            . '<td><select name="breadcrumbs">'
            . '<option value="show" ' . ($breadcrumbs == 'show' ? $selected : '') . '>'
			. __('Show', 'shcreate') . '</option>'
            . '<option value="hide" ' . ($breadcrumbs == 'hide' ? $selected : '') . '>'
			. __('Hide', 'shcreate') . '</option>'
            . '</select></td><td></td></tr>'

			. '<tr><td>' . __("Override Top Menu?", "shcreate") . '</td>'
            . '</td><td><input type="checkbox" name="enable_menubar" value="yes" ' . $menubar_check . '" />'
            . '</td><td></td></tr>'

			. '<tr><td>' . __("Choose Top Menu Type", "shcreate") . '</td>'
            . '<td><select name="menubar">'
            . '<option value="classic" ' . ($menubar == 'classic' ? $selected : '') . '>'
            . __('Classic', 'shcreate') . '</option>'
            . '<option value="centerlayered" ' . ($menubar == 'centerlayered' ? $selected : '') . '>'
            . __('Center Layered', 'shcreate') . '</option>'
			. '<option value="leftlayered" ' . ($menubar == 'leftlayered' ? $selected : '') . '>'
            . __('Left Layered', 'shcreate') . '</option>'
			. '<option value="rightlayered" ' . ($menubar == 'rightlayered' ? $selected : '') . '>'
            . __('Right Layered', 'shcreate') . '</option>'
            . '</select></td><td></td></tr>'

			. '<tr><td>' . __("Override Top Menu Shadow?", "shcreate") . '</td>'
            . '</td><td><input type="checkbox" name="enable_menushadow" value="yes" ' . $menushadow_check . '" />'
            . '</td><td></td></tr>'

            . '<tr><td>' . __("Menu Shadow Visibility", "shcreate") . '</td>'
            . '<td><select name="menushadow">'
            . '<option value="show" ' . ($menushadow == 'show' ? $selected : '') . '>'
            . __('Show', 'shcreate') . '</option>'
            . '<option value="hide" ' . ($menushadow == 'hide' ? $selected : '') . '>'
            . __('Hide', 'shcreate') . '</option>'
            . '</select></td><td></td></tr>'


			.'</table></div>';
	}


	/**
	 * Custom meta box for Blog
	**/
	public function blog_custom_box() {
        global $post;
        $blog_layout = get_post_meta( $post->ID, 'blog_layout', true);
        $blog_stand_sidebar = get_post_meta( $post->ID, 'blog_stand_sidebar', true);
        if ($blog_layout == '') { // default layout
            $blog_layout = 'large';
        }

        if ($blog_stand_sidebar == '') { // default to right
            $blog_stand_sidebar = 'right';
        }

        $selected = 'selected="selected"';
        echo '<div id="page-options-blog"><table class="meta-table">'
           . '<tr><td>' . __( "Blog Layout: ", "shcreate" ) . '</td>'
           . '</td><td><select id="blog-layout" name="blog-layout">'
           . '<option value="large" ' . ($blog_layout == 'large' ? $selected : '') . '>' 
		   . __('Large (top) media', 'shcreate') . '</option>'
           . '<option value="medium" ' . ($blog_layout == 'medium' ? $selected : '') . '>' 
		   . __('Medium (left) media', 'shcreate') . '</option>'
           . '</select></td><td></td></tr>'

           . '<tr><td>' .  __( "Sidebar: " , "shcreate" ) . '</td>'
           . '<td><select id="blog-stand-sidebar" name="blog-stand-sidebar">'
           .'<option value="none" ' . ($blog_stand_sidebar == 'none' ? $selected : '') . '>'
           . __('No Sidebar', 'shcreate') . '</option>'
           .'<option value="right" ' . ($blog_stand_sidebar == 'right' ? $selected : '') . '>'
           . __('Right Sidebar', 'shcreate') . '</option>'
           .'<option value="left" ' . ($blog_stand_sidebar == 'left' ? $selected : '') . '>'
           . __('Left Sidebar', 'shcreate') . '</option>'
           .'</select></td><td></td></tr></table></div>';
    }

	/**
	 * Custom meta box for Blog Timeline
	**/
	public function blogtime_custom_box() {
		global $post;
        $blog_layout = get_post_meta( $post->ID, 'blog_layout', true);
        $blog_time_sidebar = get_post_meta( $post->ID, 'blog_time_sidebar', true);

        if ($blog_time_sidebar == '') { // default to right
            $blog_time_sidebar = 'right';
        }

        $selected = 'selected="selected"';
        echo '<div id="page-options-blogtime"><table class="meta-table">'
           . '<tr><td>' .  __( "Sidebar: " , "shcreate" ) . '</td>'
           . '<td><select id="blog-time-sidebar" name="blog-time-sidebar">'
           .'<option value="none" ' . ($blog_time_sidebar == 'none' ? $selected : '') . '>'
           . __('No Sidebar', 'shcreate') . '</option>'
           .'<option value="right" ' . ($blog_time_sidebar == 'right' ? $selected : '') . '>'
           . __('Right Sidebar', 'shcreate') . '</option>'
           .'<option value="left" ' . ($blog_time_sidebar == 'left' ? $selected : '') . '>'
           . __('Left Sidebar', 'shcreate') . '</option>'
           .'</select></td><td></td></tr></table></div>';
    }

	/** 
	  * Custom meta box for Blog Grid
	 **/
	public function bloggrid_custom_box() {
		global $post;
		$blog_grid = get_post_meta( $post->ID, 'blog_grid', true);
		$blog_sidebar = get_post_meta( $post->ID, 'blog_sidebar', true);
		if ($blog_grid == '') { // default grid
            $blog_grid = '2';
        }

        if ($blog_sidebar == '') { // default to none
            $blog_sidebar = 'none';
        }

        $selected = 'selected="selected"';
		echo '<div id="page-options-bloggrid"><table class="meta-table">'
           . '<tr><td>' . __( "Grid Columns: ", "shcreate" ) . '</td>'
           . '</td><td><select id="blog-grid" name="blog-grid">'
           . '<option value="2" ' . ($blog_grid == '2' ? $selected : '') . '>' . __('2 Columns', 'shcreate') . '</option>'
           . '<option value="3" ' . ($blog_grid == '3' ? $selected : '') . '>' . __('3 Columns', 'shcreate') . '</option>'
           . '<option value="4" ' . ($blog_grid == '4' ? $selected : '') . '>' . __('4 Columns', 'shcreate') . '</option>'
           . '</select></td><td></td></tr>'

		   . '<tr><td>' .  __( "Sidebar: " , "shcreate" ) . '</td>'
           . '<td><select id="blog-sidebar" name="blog-sidebar">'
           .'<option value="none" ' . ($blog_sidebar == 'none' ? $selected : '') . '>'
           . __('No Sidebar', 'shcreate') . '</option>'
           .'<option value="right" ' . ($blog_sidebar == 'right' ? $selected : '') . '>'
           . __('Right Sidebar', 'shcreate') . '</option>'
           .'<option value="left" ' . ($blog_sidebar == 'left' ? $selected : '') . '>'
           . __('Left Sidebar', 'shcreate') . '</option>'
           .'</select></td><td></td></tr></table></div>';
	}

    /**
     * Custom meta box for Portfolios
     */
    public function portfolio_custom_box() {
        global $post;

        $portfolio_ratio = get_post_meta( $post->ID, 'portfolio_ratio', true);
		$portfolio_size = get_post_meta( $post->ID, 'portfolio_size', true);
        $portfolio_grid = get_post_meta( $post->ID, 'portfolio_grid', true);
		$portfolio_rpp = get_post_meta( $post->ID, 'portfolio_rpp', true);
		$portfolio_sidebar = get_post_meta( $post->ID, 'portfolio_sidebar', true);
		$display_cat = get_post_meta( $post->ID, 'display_cat', true);
		$portfolio_cat = get_post_meta( $post->ID, 'portfolio_cat', true);

        if ($portfolio_ratio == '') {  // default ratio
            $portfolio_ratio = 'standard';
        }

		if ($portfolio_grid == '') { // default grid
			$portfolio_grid = '3';
		}

		if ($portfolio_rpp == '') { // default results per page
			$portfolio_rpp = 9;
		}

		if ($portfolio_sidebar == '') { // default to none
			$portfolio_sidebar = 'none';
		}

		if ($display_cat == '') { // default to show
			$display_cat = 'show';
		}

		$selected = 'selected="selected"';

        echo '<div id="page-options-portfolio"><table class="meta-table">'
           . '<tr><td>' . __('Thumbnail Aspect:', 'shcreate') . '</td><td><select id="portfolio-ratio" name="portfolio-ratio">'

		   . '<option value="standard" ' . ($portfolio_ratio == 'standard' ? $selected : '') 
		   . '>' . __('Standard 4:3', 'shcreate') .'</option>'
		   . '<option value="square" ' . ($portfolio_ratio == 'square' ? $selected : '') 
		   . '>' . __('Square 1:1', 'shcreate') . '</option>'
		   . '<option value="widescreen" ' . ($portfolio_ratio == 'widescreen' ? $selected : '') 
		   . '>' . __('Widescreen 16:9', 'shcreate') . '</option>'
		   . '<option value="panoramic" ' . ($portfolio_ratio == 'panoramic' ? $selected : '') 
		   . '>' . __('Panoramic 3:1', 'shcreate') . '</option>'
		   . '<option value="tall" ' . ($portfolio_ratio == 'tall' ? $selected : '') 
		   . '>' . __('Tall 2:3', 'shcreate') . '</option>'
		   . '</select></td></tr>';

		echo '<tr><td>' . __( "Grid Size: ", "shcreate" ) . '</td>'
		   . '<td><select id="portfolio-size" name="portfolio-size">'
		   . '<option value="nested" ' . ($portfolio_size == 'nested' ? $selected : '') 
		   . '>' . __('Inline', 'shcreate') . '</option>'
		   . '<option value="full" ' . ($portfolio_size == 'full' ? $selected : '') 
		   . '>' . __('Full Width', 'shcreate') . '</option>'
		   . '</select></td><td></td></tr>';

        echo '<tr><td>' . __( "Grid Columns: ", "shcreate" ) . '</td>'
           . '</td><td><select id="portfolio-grid" name="portfolio-grid">'
		   . '<option value="2" ' . ($portfolio_grid == '2' ? $selected : '') . '>' . __('2 Columns', 'shcreate') . '</option>'
		   . '<option value="3" ' . ($portfolio_grid == '3' ? $selected : '') . '>' . __('3 Columns', 'shcreate') . '</option>'
		   . '<option value="4" ' . ($portfolio_grid == '4' ? $selected : '') . '>' . __('4 Columns', 'shcreate') . '</option>'
		   . '<option value="5" ' . ($portfolio_grid == '5' ? $selected : '') . '>' . __('5 Columns', 'shcreate') . '</option>'
		   . '<option value="6" ' . ($portfolio_grid == '6' ? $selected : '') . '>' . __('6 Columns', 'shcreate') . '</option>'
           . '</select></td><td></td></tr>';
		
		echo '<tr><td>' . __( "Results Per Page: ", "shcreate" ) . '</td>'
		   . '</td><td><select id="portfolio-rpp" name="portfolio-rpp">';
		for($i = 1; $i < 51; $i++) {
			echo '<option value="' . $i . '" ' . ($i == $portfolio_rpp ? $selected : '') . '>' . $i . '</option>';
		}
		echo '</select></td><td></td></tr>';
		
		echo '<tr><td>' .  __( "Sidebar: " , "shcreate" ) . '</td>'
			. '<td><select id="portfolio-sidebar" name="portfolio-sidebar">'
			.'<option value="none" ' . ($portfolio_sidebar == 'none' ? $selected : '') . '>' 
			. __('No Sidebar', 'shcreate') . '</option>'
			.'<option value="right" ' . ($portfolio_sidebar == 'right' ? $selected : '') . '>' 
			. __('Right Sidebar', 'shcreate') . '</option>'
			.'<option value="left" ' . ($portfolio_sidebar == 'left' ? $selected : '') . '>' 
			. __('Left Sidebar', 'shcreate') . '</option>'
			. '</select></td><td></td></tr>'

			. '<tr><td>' . __( "Category Bar: ", "shcreate" ) . '</td>'
			. '<td><select id="display_cat" name="display_cat">'
			. '<option value="show" ' . ($display_cat == 'show' ? $selected : '') . '>'
			. __('Show Categories', 'shcreate') . '</option>'
			. '<option value="hide" ' . ($display_cat == 'hide' ? $selected : '') . '>'
            . __('Hide Categories', 'shcreate') . '</option>'
			.'</select></td><td></td></tr>'
			
			. '<tr><td>' . __( "Categories: ", "shcreate" ) . '</td>'
			. '<td><input type="text" class="widefat" name="portfolio-cat" value="' . $portfolio_cat . '" /></td>'
			. '</tr><tr>'
			. '<td colspan="2">' . __( "<b>Note:</b> Seperate categories with commas, leave blank for all.  If you specify categories, <i>Results Per Page</i> will be ignored.", "shcreate") , '</td></tr>'
			. '</table></div>';

    }

	/** 
     * Save custom post data 
     */
    public function save_postdata( $post_id ) {
        global $post;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        if ( isset($_POST['post_type']) ) {
            switch ($_POST['post_type']) {
				case 'people':
					update_post_meta( $post_id, 'person_link', sanitize_text_field( $_POST['person_link']) );
					update_post_meta( $post_id, 'person_name', sanitize_text_field( $_POST['person_name']) );
					update_post_meta( $post_id, 'person_title', sanitize_text_field( $_POST['person_title']) );
					update_post_meta( $post_id, 'person_quick', sanitize_text_field( $_POST['person_quick']) );
                    $san_link = array();
                    $san_icon = array();
                    if (isset($_POST['social_link'])) {
                        foreach ($_POST['social_link'] as $k => $v) {
                            $san_link[] = sanitize_text_field( $v );
                        }
                        $san_icon = array();
                        foreach ($_POST['social_icon'] as $k => $v) {
                            $san_icon[] = sanitize_text_field( $v );
                        }
                    }
                        update_post_meta( $post_id, 'social_icon', $san_icon);
                        update_post_meta( $post_id, 'social_link', $san_link);
                    break;

				 // All Pages meta
                case 'page':
					// Override Options for all pages
					$enable_width = isset($_POST['enable_width']) ? sanitize_text_field($_POST['enable_width']) : 'no';
					$enable_bread = isset($_POST['enable_bread']) ? sanitize_text_field($_POST['enable_bread']) : 'no';
					$enable_menubar = isset($_POST['enable_menubar']) ? sanitize_text_field($_POST['enable_menubar']) : 'no';
					$enable_menushadow = isset($_POST['enable_menushadow']) ? 
						sanitize_text_field($_POST['enable_menushadow']) : 'no';

					update_post_meta( $post_id, 'enable_width', $enable_width);
					update_post_meta( $post_id, 'enable_bread', $enable_bread);
					update_post_meta( $post_id, 'page_width', sanitize_text_field( $_POST['page_width']) );
					update_post_meta( $post_id, 'breadcrumbs', sanitize_text_field( $_POST['breadcrumbs']) );
					update_post_meta( $post_id, 'enable_menubar', $enable_menubar );
					update_post_meta( $post_id, 'menubar', sanitize_text_field( $_POST['menubar']) );
					update_post_meta( $post_id, 'enable_menushadow', $enable_menushadow );
					update_post_meta( $post_id, 'menushadow', sanitize_text_field( $_POST['menushadow']) );
	
                    if ($_POST['page_template'] == 'single/single-portfolio.php'
						|| $_POST['page_template'] == 'single/single-portfolio2.php'		
						|| $_POST['page_template'] == 'single/single-portfolio3.php'
					) {
                        update_post_meta( $post_id, 'portfolio_grid', sanitize_text_field( $_POST['portfolio-grid']) );
						update_post_meta( $post_id, 'portfolio_size', sanitize_text_field( $_POST['portfolio-size']) );
                        update_post_meta( $post_id, 'portfolio_ratio', sanitize_text_field( $_POST['portfolio-ratio']) );
						update_post_meta( $post_id, 'portfolio_rpp', sanitize_text_field( $_POST['portfolio-rpp']) );
						update_post_meta( $post_id, 'portfolio_sidebar', sanitize_text_field( $_POST['portfolio-sidebar']) );
						update_post_meta( $post_id, 'display_cat', sanitize_text_field( $_POST['display_cat']) );
						update_post_meta( $post_id, 'portfolio_cat', sanitize_text_field( $_POST['portfolio-cat']) );
                    } elseif ($_POST['page_template'] == 'single/single-bloggrid.php') {
						update_post_meta( $post_id, 'blog_grid', sanitize_text_field( $_POST['blog-grid']) );
						update_post_meta( $post_id, 'blog_sidebar', sanitize_text_field( $_POST['blog-sidebar']) );
					} elseif ($_POST['page_template'] == 'single/single-blog.php') {
                        update_post_meta( $post_id, 'blog_layout', sanitize_text_field( $_POST['blog-layout']) );
                        update_post_meta( $post_id, 'blog_stand_sidebar', sanitize_text_field( $_POST['blog-stand-sidebar']) );
                    } elseif ($_POST['page_template'] == 'single/single-blogtime.php') {
						update_post_meta( $post_id, 'blog_time_sidebar', sanitize_text_field( $_POST['blog-time-sidebar']) );
					}
				break;

				// posts & portfolio_entry featured videos
				case 'post':
				case 'portfolio_entry':
					update_post_meta( $post_id, 'video_type', sanitize_text_field( $_POST['video-type']) );
					update_post_meta( $post_id, 'video_id', sanitize_text_field( $_POST['video-id']) );
					update_post_meta( $post_id, 'video_poster', sanitize_text_field( $_POST['video-poster']) );
				break;
			}
		}
	}

}
