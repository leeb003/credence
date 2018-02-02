<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Theme settings needed for layout
 */
global $themeSettings;
global $shcreate;
$sidebar = false;
if (isset($_GET['sidebarpos'])) {               // Sidebar for demo purposes
	$sidebarpos = intval($_GET['sidebarpos']);
	if ($sidebarpos == '1') {  // No Sidebar
		$mainClass= 'col-md-12';
		$sidebar = false;
	} elseif ($sidebarpos == '2') {  // Left
		$mainClass = 'col-md-8 pull-right main-div';
        $sidebarClass = 'sidebar-left';
        $sidebar = true;
	} else {           // Right sidebar
		$mainClass = 'col-md-8';
        $sidebarClass = 'sidebar-right';
        $sidebar = true;
	}
} else {          // normal theme options
	if (isset($shcreate) && $shcreate['woo-sidebar'] == 2) { // Left sidebar 
    	$mainClass = 'col-md-8 pull-right main-div';
    	$sidebarClass = 'sidebar-left';
		$sidebar = true;
	} elseif (isset($shcreate) && $shcreate['woo-sidebar'] == 1) { // Right sidebar
    	$mainClass = 'col-md-8';
    	$sidebarClass = 'sidebar-right';
		$sidebar = true;
	} elseif (isset($shcreate) && $shcreate['woo-sidebar'] == 3) { // No Sidebar
		$mainClass= 'col-md-12';
		$sidebar = false;
	}
}

// Change number of products per row
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		global $shcreate;
		$GLOBALS['wooColumns'] = 3; // Set global for other sections to access
		if (isset($_GET['sidebarpos']) ) {  // for the demo
			if ($_GET['sidebarpos'] == 2
			|| $_GET['sidebarpos'] == 3
			) {
				$GLOBALS['wooColumns'] = 3;
			}
		} elseif ($shcreate['woo-sidebar'] == 3) {  // builtin setting
			$GLOBALS['wooColumns'] = 4; 
		}
		return $GLOBALS['wooColumns']; 
	}
}

get_header( 'shop' ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );

		//do_action( 'woocommerce_archive_description' );
		/* commented out woo_commerce_archive_description and calling it's functions directly
		 * includes/wc-template-hooks.php adds action  woocommerce_taxonomy_archive_description and 
		 * woocommerce_product_archive_description to it and the product function does a wpautop
		 * which screws up Visual Composer version 4.4 output.  If they fix it, just re-enable the
		 * do_action above and remove the code below.  Code below is taken from 
		 * function woocommerce_product_archive_description() and wpautop is commented out.
		 */

		do_action('woocommerce_taxonomy_archive_description');
		if ( is_post_type_archive( 'product' ) && get_query_var( 'paged' ) == 0 ) {
            $shop_page   = get_post( wc_get_page_id( 'shop' ) );
            if ( $shop_page ) {
                //$description = wpautop( do_shortcode( $shop_page->post_content ) );
				$description = do_shortcode( $shop_page->post_content );
                if ( $description ) {
                    echo '<div class="page-description">' . $description . '</div>';
                }
            }
        }

		/* end of modification to woocommerce_archive_description */

	?>
		<?php // Surround our shop in our nested container, row and col ?>
		<div class="nested-container">
			<div class="row">
				<div class="row spacer"></div>
				<div class= "<?php echo $mainClass; ?>">



		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<!-- <h1 class="page-title"><?php //woocommerce_page_title(); ?></h1> -->

		<?php endif; ?>

		<?php //do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php $clear = 0; ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php 
					$clear++;
					if ($clear == $GLOBALS['wooColumns']) {
						echo '<div class="clearfix"></div>';
						$clear = 0;
					}
					?>
				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php if ($sidebar == true) {  // Only show if set to show sidebar ?>

		</div>
		<div class="col-md-4 sidebar <?php echo $sidebarClass;?>">
	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>

	<?php } // end sidebar check ?>

		</div>
	</div>
</div>

<?php get_footer( 'shop' ); ?>
