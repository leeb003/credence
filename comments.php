<?php
/**
  * The template for displaying Comments
  */
require_once get_template_directory() . '/theme/theme-comments.php';
$themeComments = new Comments();

if ( post_password_required() )
    return;
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h4 class="comments-title">
            <?php
				echo __('Comments', 'shcreate') . ' (' . number_format_i18n( get_comments_number() ) . ')'; 
            ?>
        </h4>

        <ol class="comment-list">
            <?php
                wp_list_comments( array(
                    'style'       => 'ol',
					'callback'    => array($themeComments, 'commentList'),
                    'short_ping'  => true,
                    'avatar_size' => 75,
					'reply_text'  => '<i class="fa fa-reply"></i> Reply'
                )  );
            ?>
        </ol><!-- .comment-list -->

        <?php
            // Are there comments to navigate through?
            if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
        ?>
        <nav class="navigation comment-navigation" role="navigation">
            <h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'shcreate' ); ?></h1>
            <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'shcreate' ) ); ?></div>
            <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'shcreate' ) ); ?></div>
        </nav><!-- .comment-navigation -->
        <?php endif; // Check for comment navigation ?>

        <?php if ( ! comments_open() && get_comments_number() ) : ?>
        <p class="no-comments"><?php _e( 'Comments are closed.' , 'shcreate' ); ?></p>
        <?php endif; ?>

    <?php endif; // have_comments() ?>
	<div class="blog-comments">
    	<?php 
			$themeComments->createForm(); 
		?>
	</div>

</div><!-- #comments -->
