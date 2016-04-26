<?php
/*
 * Template Name: Blog Standard
 */
	get_header();
	global $themeSettings;
	global $shcreate;
	global $post;

	$page_id = get_queried_object_id();
    $title   = get_the_title($page_id);
	// Results per page are set in Settings -> Reading for blog
    $meta    = get_post_meta($page_id);
    $layout = $meta['blog_layout'][0];
    $sidebar = $meta['blog_stand_sidebar'][0];

	$padding = '';
    if ($layout == 'medium') {
        $padding = 'no-padding';
		if ($sidebar != 'none') {
			add_filter( 'excerpt_length', array($themeSettings, 'custom_excerpt_lengthb'), 999 );
		}
    }

	$topPull = '';
    $bar = 'right';                // default sidebar   
    $top = 'col-md-8 ' . $padding;  // default grid holder (to go with sidebar)

	if ($sidebar == 'left') {
        $top = 'col-md-8 ' . $padding;
        $topPull = 'pull-right main-div';
    } elseif ($sidebar == 'none') {
        $top = 'col-md-12 ' . $padding;
    }
?>
<?php
/* Theme overrides for pages */
$showbread = false;
if (isset($meta['enable_bread']) && $meta['enable_bread'][0] == 'yes') {
    if ($meta['breadcrumbs'][0] == 'show') {
        $showbread = true;
    }
} else { // normal theme options progression
    if ($shcreate['breadcrumb-enable'] == 1) {
        $showbread = true;
    }
}

if ($showbread == true) { ?>
<div class="title-section">
	<div class="nested-container">
		<div class="row">
    		<div class="col-md-12">
				<h1><?php echo $title; ?></h1>
				<?php $themeSettings->the_breadcrumb(); ?>
			</div>
		</div>
    </div>
</div>
<?php } ?>

<div class="nested-container">
	<div class="row">
		<div class="col-md-12 spacer"></div>
			<div class="<?php echo $top . ' ' . $topPull; ?>">
			<?php
			if (is_front_page()) {
				$current_page = get_query_var('page') ? get_query_var('page') : 1;
			} else {
				$current_page = max(1, get_query_var('paged'));
			}
			// page content, if there is any
			setup_postdata( $post );
			the_content();
			wp_reset_postdata( $post );

			// loop
			$args = array(
				'post_type' => 'post',
				'paged'     => $current_page
			);
			$my_query = new WP_Query($args);
			if( $my_query->have_posts() ):
   			while( $my_query->have_posts() ): $my_query->the_post();
				global $more;
                $more = 0;
			?>
                <?php get_template_part( 'loop', get_post_format() ); ?>
            <?php endwhile; ?>
            <?php $themeSettings->theme_paging_nav($my_query, $current_page); ?>

            <?php else : ?>
                <?php the_content(); //get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>
			<?php wp_reset_postdata() ;?>

        </div>

		<?php if ($sidebar == 'right') { ?>
        <div class="col-md-4 sidebar sidebar-right">
            <?php get_sidebar(); ?>
        </div>
        <?php } elseif ($sidebar == 'left') { ?>
        <div class="col-md-4 sidebar sidebar-left">
            <?php get_sidebar(); ?>
        </div>
        <?php } ?>
	</div>
</div>
<br />

<?php get_footer(); ?>

<?php 
/**
 * Custom Excerpt Length
**/
function custom_excerpt_length( $length ) {
            return 20;
        }
