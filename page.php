<?php
	get_header();
	global $themeSettings;
	global $shcreate;
	if (isset($shcreate) && $shcreate['blog-right-left'] == 2) { // Left sidebar 
    	$mainClass = 'col-md-8 pull-right main-div';
    	$sidebarClass = 'sidebar-left';
    	$sidebarShow = true;
	} elseif (isset($shcreate) && $shcreate['blog-right-left'] == 3) { // No sidebar 
    	$mainClass = 'col-md-12';
    	$sidebarClass = '';
    	$sidebarShow = false;
	} else {
    	$mainClass = 'col-md-8';
    	$sidebarClass = 'sidebar-right';
    	$sidebarShow = true;
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
                <h1>
                    <?php the_title(); ?>
                </h1>
                <?php $themeSettings->the_breadcrumb(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="nested-container">
	<div class="row spacer"></div>
	<div class="row">
    	<div class="row">
		<?php
			// woo commerce cart, account and any pages there that don't need sidebars
        	if ( function_exists( 'is_woocommerce' ) && is_cart()
            	|| function_exists( 'is_woocommerce' ) && is_checkout()
            	|| function_exists( 'is_woocommerce' ) && is_account_page()
            	|| is_singular('people')  // Single page view for people 
        	) {
    	?>

			<div class="col col-md-12">
				<?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', get_post_format() ); ?>
                <?php endwhile; ?>	
				 <?php endif; ?>
			</div>
			
		<?php } else { ?>
				
    		<div class="<?php echo $mainClass;?>">
        		<?php if ( have_posts() ) : ?>
        		<?php while ( have_posts() ) : the_post(); ?>
          			<?php get_template_part( 'content', get_post_format() ); ?>
        		<?php endwhile; ?>

        		<?php $themeSettings->theme_paging_nav(); ?>

        		<?php else : ?>
            		<?php get_template_part( 'content', 'none' ); ?>
        		<?php endif; ?>

     		</div>
			
			<?php if ($sidebarShow) {  // Enable sidebar ?>
            <div class="col-md-4 sidebar <?php echo $sidebarClass;?>">
                <?php get_sidebar(); ?>
            </div>
            <?php } ?>
		<?php } ?>

		</div>
	</div>
</div>
<br />

<?php get_footer(); ?>
