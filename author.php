<?php
	get_header();
	global $themeSettings;
	global $shcreate;

	$padding = '';
	if ($shcreate['blog-layout'] == '2') { // no padding for medium & no sidebar 
    	$padding = 'no-padding';
		if (isset($shcreate) && $shcreate['blog-right-left'] != 3) { // Sidebar and medium size posts
        	add_filter( 'excerpt_length', array($themeSettings, 'custom_excerpt_lengthb'), 999 );
    	}
	}

	if (isset($shcreate) && $shcreate['blog-right-left'] == 2) { // Left sidebar 
    	$mainClass = 'col-md-8 pull-right main-div ' . $padding;
    	$sidebarClass = 'sidebar-left';
    	$sidebarShow = true;
	} elseif (isset($shcreate) && $shcreate['blog-right-left'] == 3) { // No sidebar 
    	$mainClass = 'col-md-12 ' . $padding ;
    	$sidebarClass = '';
    	$sidebarShow = false;
	} else {
    	$mainClass = 'col-md-8 ' . $padding;
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

					<?php
                        /*
                         * Queue the first post, that way we know what author
                         * we're dealing with (if that is the case).
                         *
                         * We reset this later so we can run the loop properly
                         * with a call to rewind_posts().
                         */
                        the_post();

                        printf( __( 'All posts by %s', 'shcreate' ), get_the_author() );
                    ?>

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
		<div class="<?php echo $mainClass;?>">
			<?php
			/*
             * Since we called the_post() above, we need to rewind
             * the loop back to the beginning that way we can run
             * the loop properly, in full.
             */
             rewind_posts();
			?>
            <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'loop', get_post_format() ); ?>
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

	</div>
</div>
<br />

<?php get_footer(); ?>
