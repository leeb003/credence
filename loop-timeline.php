<?php
/**
 * The timeline template for displaying content called from loop.php
 *
 */
global $themeSettings;
$audio = '';
global $timelineCount;
global $countSet;
if (isset($_GET['count']) && !isset($countSet)) {
	$timelineCount = intval($_GET['count']);
	$countSet = true;
}

$class = '';            
if ($timelineCount&1) {
	$class = 'timeline-inverted';       // Right side entries
}
?>

<li id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>
	<div class="timeline-badge"><i class="fa fa-bookmark-o"></i></div>
	<div class="timeline-panel" >
		<?php if ($themeSettings->has_featured_video(get_the_ID() ) ) {   // Our Builtin Featured Video
            echo $themeSettings->the_featured_video(get_the_ID());
		?>
        <?php } elseif (has_post_format( 'gallery' )) {
            $themeSettings->add_bxslider();
        ?>

        <?php } elseif ( has_post_thumbnail() && ! post_password_required() ) { ?>
        <div class="entry-thumbnail">
            <?php
            $post_thumbnail_id = get_post_thumbnail_id();
            $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
            ?>
			<?php $title = get_the_title(); ?>
            <a href="<?php echo $post_thumbnail_url; ?>" class="dynamic-fancybox fancybox" 
                title="<?php echo $title; ?>"><?php the_post_thumbnail(); ?></a>
        </div>
        <?php } ?>

		<div class="timeline-post-content">
			<h4 class="post-title entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>

		<?php if ( has_post_format( 'audio')) {    // Check for audio format and look for the first audio shortcode
                $content = get_the_content();
                if (has_shortcode($content, 'audio')) {
                    $audio = $themeSettings->add_audio_player();
                    $audio .= '<br />';
                }
            }
        ?>

		<?php $themeSettings->grid_top_meta(); ?>

	    <?php 
			echo $audio;
			add_filter( 'excerpt_length', array('ThemeSettings', 'custom_excerpt_lengthb'), 999 );
			the_excerpt(); 
		?>

	    	<?php $themeSettings->grid_bottom_meta(); ?>
		</div>
	</div>
</li><!-- #post -->		
<?php $timelineCount++; ?>

