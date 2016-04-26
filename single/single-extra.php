<?php
/*
 * Template Name: Extra Page
 */
get_header();
global $themeSettings;
global $shcreate;
$page_id = get_queried_object_id();
$title   = get_the_title($page_id);

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
<article class="content">
	<div class="nested-container"> 
		<div class="row">
			<div class="col-md-12">

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
