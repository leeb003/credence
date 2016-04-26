<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Theme settings needed for layout
 */
global $themeSettings;
global $shcreate;
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

get_header( 'shop' ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

	 <?php // Surround our shop in our nested container, row and col ?>
        <div class="nested-container">
            <div class="row">
                <div class="row spacer"></div>
                <div class= "<?php echo $mainClass; ?>">


		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

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
