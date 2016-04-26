<?php
/**
 * The default template for displaying content
 *
 * Used for index/archive/search/category.
 *
 */
global $themeSettings;
global $shcreate;
global $grid_needed;
$audio = '';
$media = false;   // check for media determine layout (e.g. text posts, no images or video), only medium layouts
$metaDiv = '';
if ($shcreate['blog-layout'] == '2') {
	$metaDiv = 'medium-meta';
}
?>

<?php
// check for alternate grid layouts and display them
if ( is_page_template('single/single-bloggrid.php')
	|| $grid_needed == true
) {
	get_template_part( 'loop', 'grid' );
} elseif ( is_page_template('single/single-blog.php') ) {
	get_template_part( 'loop', 'standard' );

} elseif ( is_page_template('single/single-blogtime.php') ) {
	get_template_part( 'loop', 'timeline' );
	
} else { // display the default layout  ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php if ($shcreate['blog-layout'] == '2') { // Blog layout (medium, large is default) ?>
		<div class="row">
	<?php } ?>

    <?php 
	if ($themeSettings->has_featured_video(get_the_ID() ) ) {   // Our Builtin Featured Video
		if ($shcreate['blog-layout'] == '2') {
            $media = true;
            echo '<div class="col-md-5">';
            echo $themeSettings->the_featured_video(get_the_ID());
            echo '</div>';
        } else {
            echo $themeSettings->the_featured_video(get_the_ID());
        }
    } elseif (has_post_format( 'gallery' )) {
		if ($shcreate['blog-layout'] == '2') {
			$media = true;
			echo '<div class="col-md-5">';
        	$themeSettings->add_bxslider();
			echo '</div>';
		} else {
			$themeSettings->add_bxslider();
		}

    } elseif ( has_post_thumbnail() && ! post_password_required() ) { 
		if ($shcreate['blog-layout'] == '2') {
			$media = true;
			echo '<div class="col-md-5">';
		}
		?>
        	<div class="entry-thumbnail">
            	<?php
            	$post_thumbnail_id = get_post_thumbnail_id();
            	$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
            	?>
            	<a href="<?php echo $post_thumbnail_url; ?>"><?php the_post_thumbnail(); ?></a>
        	</div>

		<?php if ($shcreate['blog-layout'] == '2') { ?>
			</div>
		<?php } ?>

    <?php } ?>


	<?php if ( has_post_format( 'audio')) {    // Check for audio format and look for the first audio shortcode
        $content = get_the_content();
        if (has_shortcode($content, 'audio')) {
            $audio = $themeSettings->add_audio_player();
            $audio .= '<br /><br />';
        }
    } ?>

	<?php if ($shcreate['blog-layout'] == '2') { // Blog layout (medium, large is default) ?>
		<?php if ($media) { ?>
			<div class="col-md-7 medium-blog">
		<?php } else { ?>
			<div class="col-md-12 medium-blog">
		<?php } ?>
    <?php } ?>

		<h4 class="post-title entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>

		<?php echo $audio; ?>

	    <?php the_excerpt(); ?>

	<?php if ($shcreate['blog-layout'] == '2') { // Blog layout (medium, large is default) ?>
            </div>
        </div>
    <?php } ?>
		<div class="entry-meta <?php echo $metaDiv; ?>">
        	<?php $themeSettings->theme_entry_meta(); ?>
			<div class="inline-readmore"> 
				<a href="<?php the_permalink(); ?>"><?php echo __('Read more', 'shcreate'); ?> &nbsp;
					<i class="fa fa-angle-right"></i></a> 
			</div>
    	</div><!-- .entry-meta -->
    	<?php edit_post_link( __( 'Edit&nbsp;&nbsp;&nbsp;', 'shcreate' ),
		 	'<span class="edit-link ' . $metaDiv . '"> <i class="fa fa-pencil"></i> ','</span>' ); ?>

		<footer>

			<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
				<?php get_template_part( 'author-bio' ); ?>
			<?php endif; ?>
		</footer><!-- .entry-meta -->

	</article><!-- #post -->

<?php } // end default layout ?>
