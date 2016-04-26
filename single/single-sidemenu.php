<?php
/*
 * Template Name: Extra Page Off Canvas Sidebar
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
		<div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-9">
			<p class="pull-right visible-xs page_nav">
            <a href="#" class="sh-btn btn-xs" 
				data-toggle="offcanvas"><?php echo __('Toggle Nav', 'shcreate'); ?></a>
          	</p>

				<?php if ( have_posts() ) : ?>
            	<?php while ( have_posts() ) : the_post(); ?>
                	<?php the_content(); ?>
            	<?php endwhile; ?>
            	<?php endif; ?>

			</div>

			<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
				 	<?php $side_menu_name = $themeSettings->side_menu_check();  // Make sure our side menu exists ?>
                 	<?php if ($side_menu_name) { ?>
                       	<?php wp_nav_menu( array(
                           	'theme_location' => 'side-menu',
                            'container' => false,
                            'depth' => 1,
                            'menu_class' => 'offcanvas-list'
                        )); ?>
                    <?php } else { ?>
                      	<div class="msg">Please make sure you have a side menu set up under appearance. </div> 
                    <?php } ?>
        	</div><!--/span-->

		</div>
	</div>
</article>

<?php get_footer(); ?>
