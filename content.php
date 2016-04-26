<?php
/**
 * The default template for displaying content
 *
 * Used for index/archive/search/category.
 *
 */
global $themeSettings;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (!is_page() && 'people' != get_post_type() ) { // don't display the title for pages ?>
	<h4 class="post-title entry-title"><?php the_title(); ?></h4>
	<?php } ?>

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_content( '', FALSE, ''); // Hide the read more and display in the meta ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php the_content( '', FALSE, ''); // Hide the read more and display in the meta ?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<?php
        // woo commerce cart, account and any pages there that don't need meta
        if ( function_exists( 'is_woocommerce' ) && is_cart()
        	|| function_exists( 'is_woocommerce' ) && is_checkout()
           	|| function_exists( 'is_woocommerce' ) && is_account_page()
			|| is_singular('people')  // Single page view for people 
        ) {
    ?>

	<?php } else { ?>
		<?php if (!is_page()) { ?>
	<div class="entry-meta">
        <?php $themeSettings->theme_entry_meta(); ?>
    </div><!-- .entry-meta -->
	<?php wp_link_pages(); // For posts using nextpage tags ?>

    <?php edit_post_link( __( 'Edit&nbsp;&nbsp;&nbsp;', 'shcreate' ),
		 '<span class="edit-link"> <i class="fa fa-pencil"></i> ','</span>' ); ?>
		<?php } ?>
	<?php } ?>

	<footer>

		<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
			<?php get_template_part( 'author-bio' ); ?>
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post -->
