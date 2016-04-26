<?php 
	get_header(); 
	global $shcreate;	
	$page = get_post($themeSettings->post_id);
?>
<article class="content">
<div class="nested-container">
	<div class="row">
		<div class="col-md-12">
			<?php echo apply_filters( 'the_content', $page->post_content); ?>
    	</div>
	</div>
</div>
</article>
<?php get_footer(); ?>
