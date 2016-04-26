<?php
/**
 * Woo Commerce modifications and hooks
 */

class ThemeCommerce {
	// Properties
	private $perpage;

	// Methods

	public function __construct() {
		// Display products per page.
		add_action( 'woocommerce_before_shop_loop', array( &$this, 'woocommerce_catalog_page_ordering'), 20 );
		add_filter('loop_shop_per_page', array(&$this, 'sort_by_page'));

		// Override image settings on category pages
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
		add_action( 'woocommerce_before_shop_loop_item_title', array(&$this, 'mod_template_loop_product_thumbnail'), 10);

		// Add cart to top menu
		add_filter('wp_nav_menu_items', array(&$this, 'add_cart_to_nav'), 10, 2);

		// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
		add_filter('add_to_cart_fragments', array(&$this, 'woocommerce_header_add_to_cart_fragment'));

	}

	// Ajax update cart icon in navbar
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		if ($woocommerce->cart->cart_contents_count > 0) {
			$count = $woocommerce->cart->cart_contents_count;
		} else {
			$count = 0;
		}
		ob_start();
		?>

		<a class="top-cart dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-shopping-cart"></i> <span class="top-cart-amount"><?php echo $count;?></span></a>

		<?php
		$fragments['a.top-cart'] = ob_get_clean();
		return $fragments;
	}

	// Add cart to top navigation only if woocommerce is active
	public function add_cart_to_nav($items, $args) {
		global $shcreate;
		if ($shcreate['woo-shopping-cart'] != '1') {  // check if enabled in theme options first
			return $items;
		}
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$page_id = get_queried_object_id();
            $meta    = get_post_meta($page_id);
            $classic = false;
            if (isset($meta['enable_menubar']) && $meta['enable_menubar'][0] == 'yes') {
                if ($meta['menubar'][0] == 'classic') {
                    $classic = true;
                }
            } elseif ($shcreate['nav-option'] == 1) { // Classic menu add to main nav
                $classic = true;
            }
            if (true == $classic) {
				$menu_sel = 'nav-menu';
			} else {
				$menu_sel = 'nav-menu-secondary';
			}

			if ($args->theme_location == $menu_sel) { // based on menu selection
				global $woocommerce;
				if ($woocommerce->cart->cart_contents_count > 0) {
					$active = 'active';
					$count = $woocommerce->cart->cart_contents_count;
				} else {
					$active = '';
					$count = 0;
				}

				/* Capture the_widget output and save to a variable for output in order */
				$instance = array();
				$cart_args = array(
						'before_widget' => '<div class="dropdown-menu multi-column"><div class="row"><div class="new-column col-md-12 woocommerce"><ul class="dropdown-menu"><div class="nav-woo-cart">',
						'after_widget' => '</div></ul></div></div></div>',
						'before_title' => '<span class="new-column-title">',
						'after_title' => '</span>'
				);
				ob_start();
				the_widget('WC_Widget_Cart', $instance, $cart_args);
				$shopping_cart = ob_get_contents();
				ob_end_clean();

				$items .= '<li class="menu-item dropdown yamm-pw cart-list ' . $active . '">'
					. '<a class="top-cart dropdown-toggle" data-toggle="dropdown" href="#">'
					. '<i class="fa fa-shopping-cart"></i><span class="top-cart-amount">' . $count . '</span></a>'
					. $shopping_cart . '</li>';
			}
		}
		return $items;
	}

	// Modify images shown in main product loop.  Overrides single image display so we can have multiple images
	public function mod_template_loop_product_thumbnail($size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0) {
		global $post;
		global $product;
		echo '<span class="image-swap">';

		/* Extra image on hover */
        $attachment_ids = $product->get_gallery_attachment_ids();
        if ( $attachment_ids ) {
            $image_link = wp_get_attachment_image_src ( $attachment_ids[0], $size );  // just get the first one
            echo '<img class="image-hover" src="' . $image_link[0] . '" />';
        } else { // if there are no extra images duplicate primary image
			// Copied woocommerce_get_product_thumbnail function from includes/wc-template-functions.php
        	if ( has_post_thumbnail() ) {
            	$image_link = wp_get_attachment_image_src ( get_post_thumbnail_id($post->ID), $size );
				echo '<img class="image-hover" src="' . $image_link[0] . '" />';
        	} elseif ( wc_placeholder_img_src() ) {
            	$image_link = wc_placeholder_img_src( $size );
				
				echo '<img class="image-hover" src="' . $image_link . '" />';
        	}
		}


		/* Original image */
		// Copied woocommerce_get_product_thumbnail function from includes/wc-template-functions.php
        if ( has_post_thumbnail() ) {
            echo get_the_post_thumbnail( $post->ID, $size );
        } elseif ( wc_placeholder_img_src() ) {
            echo wc_placeholder_img( $size );
		}
		
		echo '</span>';
	}


	// User selects how many products to view per page
	// via http://designloud.com/how-to-add-products-per-page-dropdown-to-woocommerce/
	public function woocommerce_catalog_page_ordering() { 
		$firstPage = get_permalink( woocommerce_get_page_id( 'shop' ) );
		$action = $firstPage;
		?>
		<form action="<?php echo $action;?>" method="POST" name="results" class="paging-form">
		<select name="woocommerce-sort-by-columns" id="woocommerce-sort-by-columns" class="sortby" onchange="this.form.submit()">
		<?php

		//  This is where you can change the amounts per page that the user will use.
		$shopCatalog_orderby = apply_filters('woocommerce_sortby_page', array(
			''		=> __('Items per page', 'shcreate'),
			'9'		=> __('Show 9 Items', 'shcreate'),
			'18' 	=> __('Show 18 Items', 'shcreate'),
			'36' 	=> __('Show 36 Items', 'shcreate'),
			'-1'	=> __('View All', 'shcreate'),
		));

		$sortby = isset($_SESSION['sortby']) ? $_SESSION['sortby'] : '';
		foreach ( $shopCatalog_orderby as $sort_id => $sort_name ) {
			echo '<option value="' . $sort_id . '" ' . selected( $sortby, $sort_id, false ) . ' >' . $sort_name . '</option>';
		}
		?>
		</select>

		</form>
		<?php   // Adrian's code
		$cookieResults = isset($_COOKIE['shop_pageResults']) ? $_COOKIE['shop_pageResults'] : '';
		if (isset($_POST['woocommerce-sort-by-columns']) 
		&& (($cookieResults <> $_POST['woocommerce-sort-by-columns']))) {
			$currentProductsPerPage = $_POST['woocommerce-sort-by-columns'];
		} else {
			$currentProductsPerPage = $cookieResults;
		}
		?>
    	<script type="text/javascript">
        	jQuery('select.sortby>option[value="<?php echo $currentProductsPerPage; ?>"]').attr('selected', true); 
    	</script>
	<?php 
	}	 

	// now we set our cookie if we need to
	public function sort_by_page($count) {
  		if (isset($_COOKIE['shop_pageResults'])) { // if normal page load with cookie
     		$count = $_COOKIE['shop_pageResults'];
  		}
  		if (isset($_POST['woocommerce-sort-by-columns'])) { //if form submitted
    		setcookie('shop_pageResults', $_POST['woocommerce-sort-by-columns'], time()+1209600, '/', $_SERVER['SERVER_NAME'], false); //this will fail if any part of page has been output- hope this works!
    		$count = $_POST['woocommerce-sort-by-columns'];
  		}
  		// else normal page load and no cookie
  		return $count;
	}



}

new ThemeCommerce();
