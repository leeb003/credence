<?php
/*
 * Template Name: Blog Timeline
 */
	get_header();
	global $themeSettings;
	global $shcreate;
	global $post;
	global $timelineCount;

	/* This page makes use of ajax load posts load-posts.js */

	$page_id = get_queried_object_id();
    $title   = get_the_title($page_id);
	// Results per page are set in Settings -> Reading for blog
    $meta    = get_post_meta($page_id);
    $sidebar = $meta['blog_time_sidebar'][0];

	$padding = '';

	$topPull = '';
    $bar = 'right';     // default sidebar   
    $top = 'col-md-8';  // default grid holder (to go with sidebar)

	if ($sidebar == 'left') {
        $top = 'col-md-8';
        $topPull = 'pull-right main-div';
    } elseif ($sidebar == 'none') {
        $top = 'col-md-12';
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
		<div class="<?php echo $top . ' ' . $topPull; ?>">
			<ul class="timeline">
				<?php
				if (is_front_page()) {  // if a blog template is set as frontpage
                	$current_page = get_query_var('page') ? get_query_var('page') : 1;
				} else {
					$current_page = max(1, get_query_var('paged'));
				}
				$args = array(
					'post_type' => 'post',
					'paged'     => $current_page,
					'orderby'   => 'date',
					'order'     => 'DESC'
				);
				$my_query = new WP_Query($args);
				if( $my_query->have_posts() ):
   				while( $my_query->have_posts() ): $my_query->the_post();
					global $more;
                	$more = 0;
				?>
                	<?php get_template_part( 'loop', get_post_format() ); ?>
            	<?php endwhile; ?>
				<?php
					$max = $my_query->max_num_pages;
                	$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
                	/* Instead of wp_localize_script (because of custom query) create function to add variables to header */
                	create_js_variables($paged, $max);
				?>

				<div class="page_nav" id="pbd-alp-load-posts">
					<a class="loadmore" href="#"><?php echo __('Load More', 'shcreate'); ?></a>
				</div>
            	<?php //$themeSettings->theme_paging_nav($my_query, $current_page); ?>

            	<?php else : ?>
                	<?php the_content(); //get_template_part( 'content', 'none' ); ?>
            	<?php endif; ?>
				<?php wp_reset_postdata() ;?>
			</ul>
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
 * Function create js variables
**/
function add_to_head() {
	create_js_variables();
	//add_action( 'wp_head', 'create_js_variables');
}

function create_js_variables($paged, $max) {
	global $timelineCount;
	$pbd_alp = array(
            'startPage'   => $paged,
            'maxPages'    => $max,
            'nextLink'    => next_posts($max, false),
			'loadingText' => __('Loading Posts...', 'shcreate'),
			'loadMore'    => __('Load More', 'shcreate'),
			'noMore'      => __('No More Posts To Load', 'shcreate'),
			'count'       => $timelineCount

    );
	echo "<script type='text/javascript'>\n";
    echo "/* <![CDATA[ */\n"; ?>
	var pbd_alp = <?php echo json_encode($pbd_alp); ?>
	<?php
	echo "\n/* ]]> */\n";
    echo "</script>\n";
}

