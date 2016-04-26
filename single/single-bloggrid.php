<?php
/*
 * Template Name: Blog Grid
 */
	get_header();
	global $themeSettings;
	global $shcreate;
	global $post;

	$page_id = get_queried_object_id();
    $title   = get_the_title($page_id);
	// Results per page are set in Settings -> Reading for blog
	$meta    = get_post_meta($page_id);
	$grid = $meta['blog_grid'][0];
	$sidebar = $meta['blog_sidebar'][0];

	$topPull = '';
	$cols = 'col-md-6'; 		   // default 2 columns
	$bar = 'right';     		   // default sidebar	
	$top = 'col-md-8 no-padding';  // default grid holder (to go with sidebar)

	if ($grid == '3') {
		$cols = 'col-md-4';
	} elseif ($grid == '4') {
		$cols = 'col-md-3';
	}

	if ($sidebar == 'left') {
		$top = 'col-md-8 no-padding';
		$topPull = 'pull-right main-div';
	} elseif ($sidebar == 'none') {
		$top = 'col-md-12 no-padding';
	}
?>

<?php
/* Theme overrides for pages */
$page_id = get_queried_object_id();
$meta    = get_post_meta($page_id);
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
	</div>
	<div class="row">
		<div class="<?php echo $top . ' ' . $topPull; ?>">
			<div class="blog-grid-full">
				<?php
				if (is_front_page()) {  // if a blog template is set as frontpage
			    	$current_page = get_query_var('page') ? get_query_var('page') : 1;
				} else {
					$current_page = max(1, get_query_var('paged'));
				}
				$args = array(
					'post_type' => 'post',
					'paged'     => $current_page
				);
				$my_query = new WP_Query($args);
				if( $my_query->have_posts() ):
					$i = 0;
   					while( $my_query->have_posts() ): $my_query->the_post();
						$i++;  // increment to apply clear fix to grid
						global $more;
                		$more = 0;
						echo '<div class="' . $cols . ' grid-post">';
					?>
                		<?php get_template_part( 'loop', get_post_format() ); ?>
						<?php echo "</div><!-- End Grid Post -->"; ?>
						<?php
                    	if ($i == $grid) {
                        	$i = 0;
                        	//echo '<div class="clearfix visible-md visible-lg"></div>';
                    	}
                	?>
            		<?php endwhile; ?>
				</div>
				<div class="clearfix"></div>
				<div class="row">
				<div class="col-md-12">
            		<?php $themeSettings->theme_paging_nav($my_query, $current_page); ?>

            		<?php else : ?>
                		<?php the_content(); //get_template_part( 'content', 'none' ); ?>
            		<?php endif; ?>
					<?php wp_reset_postdata() ;?>
        		</div>
			</div>
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
