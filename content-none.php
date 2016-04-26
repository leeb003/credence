
    <h2 class="page-title"><?php _e( 'Nothing Found', 'shcreate' ); ?></h2>

<div class="page-content">
    <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

    <p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'shcreate' ), admin_url( 'p
ost-new.php' ) ); ?></p>

    <?php elseif ( is_search() ) : ?>

    <p><?php _e( 'Sorry, but nothing matched your search. Please try again with something different.', 'shcreate' );
 ?></p>
    <?php get_search_form(); ?>

    <?php else : ?>

    <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'shcreate' ); ?></
p>
    <?php get_search_form(); ?>

    <?php endif; ?>
</div><!-- .page-content -->
