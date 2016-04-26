<?php
/*
 * Template Name: Blank Page
 */
get_header();
global $themeSettings;
global $shcreate;
$page_id = get_queried_object_id();
$title   = get_the_title($page_id);

?>

<article class="content">
	<div class="nested-container">
    	<div class="row">
        	<div class="col-md-12 blank">

            	<?php if ( have_posts() ) : ?>
            	<?php while ( have_posts() ) : the_post(); ?>
                	<?php the_content(); ?>
            	<?php endwhile; ?>
            	<?php endif; ?>

        	</div>
    	</div>
	</div>
</article>
<?php get_footer(); ?>
