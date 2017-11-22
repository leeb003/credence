<?php
/**
 * Shop breadcrumb
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_icon = '<i class="fa fa-home"></i>';
$delimiter = '   <i class="fa fa-angle-right"> </i>   ';
global $shcreate;
if ($shcreate['breadcrumb-enable'] == '1') { // Check if it's enabled ?>

<div class="title-section">
    <div class="nested-container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title">
                    <?php woocommerce_page_title(); ?>
                </h1>

<?php if ( $breadcrumb ) : ?>

	<?php echo $wrap_before; ?>

	<?php foreach ( $breadcrumb as $key => $crumb ) : ?>

		<?php echo $before; ?>

		<?php if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) : ?>
			<?php echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>'; ?>
		<?php else : ?>
			<?php echo esc_html( $crumb[0] ); ?>
		<?php endif; ?>

		<?php echo $after; ?>

		<?php if ( sizeof( $breadcrumb ) !== $key + 1 ) : ?>
			<?php echo $delimiter; ?>
		<?php endif; ?>

	<?php endforeach; ?>

	<?php echo $wrap_after; ?>

<?php endif; ?>
            </div>
        </div>
    </div>
</div>  <!-- End Title wrapper -->
<?php } // end check for breadcrumb enable ?>
<?php
