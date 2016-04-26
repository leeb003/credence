<?php
/* 
 * Single page
*/
get_header();
global $themeSettings;
global $shcreate;
if (isset($shcreate) && $shcreate['blog-right-left'] == 2) { // Left sidebar 
    $mainClass = 'col-md-8 pull-right main-div';
    $sidebarClass = 'sidebar-left';
    $sidebarShow = true;
} elseif (isset($shcreate) && $shcreate['blog-right-left'] == 3) { // No sidebar 
    $mainClass = 'col-md-12 ';
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
	<div class="row">
		<div class="col-md-12 spacer"></div>

		<?php if (is_singular('people')) {  // Single page view for people ?>

		<div class="col-md-12">
			<?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'content', get_post_format() ); ?>
                <?php comments_template(); ?>
            <?php endwhile; ?>

            <?php else : ?>
                <?php get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>
		</div>

		<?php } else { ?>				

    	<div class="<?php echo $mainClass;?>">
			<div class="prev_next">
				<?php
					$previousP = __('Previous Post', 'shcreate');
					$nextP = __('Next Post', 'shcreate');
				?>
                <?php previous_post_link('%link', "<i class='fa fa-angle-left'></i> &nbsp;&nbsp;$previousP", false); ?>    
                <?php next_post_link('%link', "$nextP &nbsp;&nbsp;<i class='fa fa-angle-right'></i>", false); ?>
            </div>

			<?php if ($themeSettings->has_featured_video(get_the_ID() ) ) {   // Our Builtin Featured Video
            echo $themeSettings->the_featured_video(get_the_ID());
        	?>
			<?php } elseif (has_post_format( 'gallery' )) {
				$themeSettings->add_bxslider();
			?>

        	<?php } elseif ( has_post_thumbnail() && ! post_password_required() ) { ?>
        		<div class="entry-thumbnail">
            		<?php the_post_thumbnail(); ?>
        		</div>
        	<?php } ?>

        	<?php if ( have_posts() ) : ?>
        	<?php while ( have_posts() ) : the_post(); ?>
          		<?php get_template_part( 'content', get_post_format() ); ?>
		  		<?php comments_template(); ?>
        	<?php endwhile; ?>

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
<br />

<?php get_footer(); ?>
