<?php
/*
 * Template Name: Portfolio Style 1
 * Retrieve the aspect and columns from meta
 */
get_header();
global $themeSettings;
global $shcreate;
$page_id = get_queried_object_id();
$meta    = get_post_meta($page_id);
// Results per page
if (isset($meta['portfolio_rpp'])) {
	$rpp = $meta['portfolio_rpp'][0];
} else {
	$rpp = 9; // default
}

$grid = $meta['portfolio_grid'][0];
$sidebar = $meta['portfolio_sidebar'][0];
$size = $meta['portfolio_size'][0];
$display_cat = isset($meta['display_cat'][0]) ? $meta['display_cat'][0] : '';
$portfolio_cat = isset($meta['portfolio_cat'][0]) ? $meta['portfolio_cat'][0] : '';

// Column Counts
$col = 'col-md-4'; // default 3 columns
if ($grid == 2) {
	$col = 'col-md-6 col-sm-6';
} elseif ($grid == 3) {
	$col = 'col-md-4 col-sm-6';
} elseif ($grid == 4) {
	$col = 'col-md-3 col-sm-6';
} elseif ($grid == 5) {
	$col = 'col-md-5ths col-sm-6';
} elseif ($grid == 6) {
	$col = 'col-md-2 col-sm-6';
}

// Image formats
$ratio = $meta['portfolio_ratio'][0];

/* 
 * Query the post, get portfolio items
*/
$dupes = 0;
// If they have categories selected
if (trim($portfolio_cat) != '') {  // query one or multiple portfolio categories
	$portfolio = '';
	$categories = '';
	$termArray = array();
	$ids = array();  // keep track of post ids to avoid and count duplicates

    $current_page = max(1, get_query_var('paged'));
	$i = 0;
    $cats_untrimmed = explode(',', $portfolio_cat);
	$cats = array_map('trim', array_map('strtolower', $cats_untrimmed) );

    foreach ($cats as $cat) {
        $args = array(
        	'post_type' => 'portfolio_entry',
            'portcat' => $cat,
            'posts_per_page' => $rpp
        );

    	$my_query = new WP_Query( $args );
    	while ( $my_query->have_posts() ) : $my_query->the_post();
			$i++;
 			if (in_array($post->ID, $ids)) { $dupes++; continue; }  // skip duplicates
        	// Pull category for each unique post using the ID 
        	$terms = get_the_terms( $post->ID, 'portcat' );

        	if ( $terms && ! is_wp_error( $terms ) ) {

            	$links = array();

            	foreach ( $terms as $term ) {
					if (in_array(strtolower($term->name), $cats)) {   // only show categories filtered
                		$links[] = $term->name;

                		// build category array for links
                		if (!array_key_exists($term->name, $termArray)) {
                    		$termArray[$term->name] = 1;
                		} else {
                    		$termArray[$term->name] = $termArray[$term->name] + 1;
                		}
					}
            	}
 
            	$tax_links = join( " ", str_replace(' ', '-', $links));
				$tax_display = join(" " , $links);
            	$tax = strtolower($tax_links);
        	} else {
            	$tax = '';
        	}
        	$thumbnail = get_the_post_thumbnail($post->ID, $ratio, array('class' => 'thumbnail img-responsive'));
        	//get post thumbnail id
        	$image_id = get_post_thumbnail_id();
        	//go get image attributes [0] => url, [1] => width, [2] => height
        	$image_url = wp_get_attachment_image_src($image_id,'', true);
        	$fullImageLink = $image_url[0];
        	$permalink = get_the_permalink($post->ID);
        	$title = get_the_title($post->ID);
        	$date_value = get_the_time('Y-m-d H:i:s', $post->ID);

        	if ($themeSettings->has_featured_video($post->ID)) {
            	$mediaLink = $themeSettings->get_featured_video_link($post->ID);
            	$faClass = "fa fa-play";
            	$fancybox = "fancybox fancybox-media";
        	} else {
            	$mediaLink = $fullImageLink;
            	$faClass = "fa fa-camera";
            	$fancybox = "fancybox";
        	}
	
       		/* Build the output, and Insert category name into portfolio-item class */
       		$portfolio .= '<div class="' . $col . ' col-sm-6 all portfolio-item '. $tax .'" data-value="' . $date_value . '">
               <div class="port-img">
                   <div class="item-overlay">
                       <div class="port-holder" data-time="' . $date_value . '" data-name="' . $title . '">
                           <a class="' . $fancybox . '" rel="' . $tax . '" title="' . $title
                               . '" href="' . $mediaLink . '">
                               <div class="port-link"> <i class="' . $faClass . '"></i> </div>
                           </a>
                           <a href="' . $permalink . '"><div class="port-zoom"><i class="fa fa-link"></i> </div></a>
                       </div>
                       <div class="port-desc">
                           <h6>' . $title . '</h6>
                           ' . $tax_display . '
                       </div>

                   </div>' . $thumbnail .'</div>
               </div>';

			$ids[] = $post->ID;  // Add id to array checking for duplicates
    	endwhile;
    	wp_reset_query();

	} // end foreach

} else { // All Portfolio Categories
	$current_page = max(1, get_query_var('paged'));
	$args = array(
		'post_type' => 'portfolio_entry',
    	'paged' => $current_page,
		'posts_per_page' => $rpp
	);

	$my_query = new WP_Query( $args );
	$portfolio = '';
	$categories = '';
	$termArray = array();
	while ( $my_query->have_posts() ) : $my_query->the_post();
		// Pull category for each unique post using the ID 
    	$terms = get_the_terms( $post->ID, 'portcat' );

    	if ( $terms && ! is_wp_error( $terms ) ) {

	    	$links = array();
			$i = 0;
        	foreach ( $terms as $term ) {
				$i++;
				if ($i > 1) { $dupes++; }  // Add to duplicates for total
	        	$links[] = $term->name;

            	// build category array for links
            	if (!array_key_exists($term->name, $termArray)) {
  	        		$termArray[$term->name] = 1;
            	} else {
                	$termArray[$term->name] = $termArray[$term->name] + 1;
            	}
        	}
 
        	$tax_links = join( " ", str_replace(' ', '-', $links));
			$tax_display = join(" " , $links);
        	$tax = strtolower($tax_links);
		} else {
        	$tax = '';
    	}
    	$thumbnail = get_the_post_thumbnail($post->ID, $ratio, array('class' => 'thumbnail img-responsive'));
    	//get post thumbnail id
    	$image_id = get_post_thumbnail_id();
    	//go get image attributes [0] => url, [1] => width, [2] => height
    	$image_url = wp_get_attachment_image_src($image_id,'', true);
    	$fullImageLink = $image_url[0];
    	$permalink = get_the_permalink($post->ID);
    	$title = get_the_title($post->ID);
		$date_value = get_the_time('Y-m-d H:i:s', $post->ID);

		if ($themeSettings->has_featured_video($post->ID)) {
			$mediaLink = $themeSettings->get_featured_video_link($post->ID);
			$faClass = "fa fa-play";
			$fancybox = "fancybox-media";
    	} else {
        	$mediaLink = $fullImageLink;
        	$faClass = "fa fa-camera";
        	$fancybox = "fancybox";
    	}

    	/* Build the output, and Insert category name into portfolio-item class */
    	$portfolio .= '<div class="' . $col . ' col-sm-6 all portfolio-item '. $tax .'" data-value="' . $date_value . '">
                <div class="port-img">
                    <div class="item-overlay">
                        <div class="port-holder" data-time="' . $date_value . '" data-name="' . $title . '">
                            <a class="' . $fancybox . '" rel="' . $tax . '" title="' . $title 
								. '" href="' . $mediaLink . '">
                                <div class="port-link"> <i class="' . $faClass . '"></i> </div>
                            </a>
                            <a href="' . $permalink . '"><div class="port-zoom"><i class="fa fa-link"></i> </div></a>
                        </div>
						<div class="port-desc">
							<h6>' . $title . '</h6>
						    ' . $tax_display . '
						</div>

                    </div>' . $thumbnail .'</div>
                </div>';
	endwhile;
	wp_reset_query();
}

// create the taxonomy links
$totalCount = 0;
$categories = '<ul class="left-options">';
foreach ($termArray as $key => $value) {
	// Get the total categories
    $totalCount = $totalCount + $value;
}
$totalCount = $totalCount - $dupes;
$categories .= '<li><a href="javascript:void(0)" title="" data-filter=".all" class="active catsort">' . __('All', 'shcreate') 
            . ' <span class="term-count">' . $totalCount . '</span></a></li>';

// loop again to get the individual taxonomies
foreach ($termArray as $key => $value) {
	$termLower = strtolower($key);
	$termLower = preg_replace('/\s+/', '-', $termLower);
	$categories .= '<li><a href="javascript:void(0)" class="catsort" title="" data-filter=".' . $termLower . '">'.$key
                . '<span class="term-count">' . $value . '</span></a></li>';
}

// add the date and name sort here for now
$categories .= '</ul><ul class="right-options"><li><a href="javascript:void(0)" data-filter="time" title="" class="date-sort">' . __('Date', 'shcreate') . '</a></li>';
$categories .= '<li><a href="javascript:void(0)" data-filter="name" title="" class="name-sort">' . __('Name', 'shcreate') . '</a></li></ul>';

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
                <h1><?php echo the_title(); ?></h1>
                <?php $themeSettings->the_breadcrumb(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<article class="content portfolio">
<?php if ($size == 'nested') { // only if nested ?>
	<div class="nested-container"> 
<?php } ?>
		<div class="row">
			<div class="col-md-12">
                	<?php the_content(); ?>

			</div>
		</div>
		<div class="row no-padding">
			<?php 
			/* Sidebar formatting, if option is set */
			if ($sidebar == 'left') { ?>
			<div class="col-md-8 pull-right main-div">

			<?php } elseif ($sidebar == 'right') { ?>
			<div class="col-md-8">

			<?php } else { ?>
            <div class="col-md-12 <?php echo $size !='nested' ? 'no-padding' : ''; ?>">

			<?php } ?>

			<?php if ($display_cat != 'hide') { ?>
                <div class="port-cats">
                    <?php echo $categories;?>
                </div>
            <?php } ?>

				<div class="clearfix"></div>
			
				<div class="portfolio-grid no-margin">
					<?php echo $portfolio; ?>
				</div>
			</div>

			<?php if ($sidebar == 'left') { ?>
			<div class="col-md-4 sidebar sidebar-left">
           		<?php get_sidebar(); ?>
        	</div>
			<?php } elseif ($sidebar == 'right') { ?>
			 <div class="col-md-4 sidebar sidebar-right">
                <?php get_sidebar(); ?>
            </div>
			<?php } ?>

			<div class="col-md-12">
				<?php $themeSettings->theme_paging_nav($my_query, $current_page); ?>
			</div>
		</div>
<?php if ($size == 'nested') { // only if nested ?>
	</div>
<?php } ?>
</article>

<?php get_footer(); ?>
