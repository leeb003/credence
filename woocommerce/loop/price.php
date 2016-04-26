<?php
/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
?>

<?php if ( $price_html = $product->get_price_html() ) : ?>
	<span class="price"><?php echo $price_html; ?></span>
<?php endif; ?>


<?php 
	// Combine rating at this level
	if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
		return;
	}
?>

<?php if ( $rating_html = $product->get_rating_html() ) : ?>
    <?php echo $rating_html; ?>
<?php endif; ?>
