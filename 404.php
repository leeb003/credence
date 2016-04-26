<?php
/**
 * The template for displaying 404 pages (Not Found)
 */

get_header(); 
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

<?php if ($shcreate['breadcrumb-enable'] == '1') { ?>
<div class="title-section">
    <div class="nested-container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title"><?php _e( 'Not Found', 'shcreate' ); ?>
                </h1>
                <?php $themeSettings->the_breadcrumb(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="nested-container">
	<div class="row">
		<div class="row spacer"></div>
    	<div class="row">
        	<div class="<?php echo $mainClass;?>">
				<div class="fourofour" 
					style="background-image:url(<?php echo get_template_directory_uri() . '/images/notfound1.jpg';?>);">
					404
				</div>
				<div class="page-content text-center">
					<p><?php _e( 'Sorry, It looks like nothing was found. Maybe try a search?', 'shcreate' ); ?></p>
					<br />
					<div class="center-search">
						<?php get_search_form(); ?>
					</div>
					<div class="spacer-large"></div>
				</div>
			</div>
			<?php if ($sidebarShow) {  // Enable sidebar ?>
			<div class="col-md-4 sidebar <?php echo $sidebarClass;?>">
            	<?php get_sidebar(); ?>
        	</div>
			<?php } ?>
    	</div>
	</div>
</div>
<br />

<?php get_footer(); ?>
