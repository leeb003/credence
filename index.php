<?php
	get_header();
	global $themeSettings;
	global $shcreate
?>
<div class="nested-container">
	<div class="row">
        <div class="col-md-12">
			<?php 
			$blog_page_id = get_option('page_for_posts');
			$page = get_post($blog_page_id);
            echo apply_filters( 'the_content', $page->post_content); ?>
        </div>
    </div>
	<div class="row">
			<div class="col-md-8">
                <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'loop', get_post_format() ); ?>
                <?php endwhile; ?>

                <?php $themeSettings->theme_paging_nav(); ?>

                <?php else : ?>
                    <?php get_template_part( 'content', 'none' ); ?>
                <?php endif; ?>

            </div>

            <div class="col-md-4 sidebar">
                <?php get_sidebar(); ?>
            </div>
	</div>
</div>
<br />

<?php get_footer(); ?>
