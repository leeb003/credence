<?php

class Comments extends ThemeSettings {
	// Properties 

	// Methods
	public function createForm() {
		global $post;
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		if ($req) {
			$req_text = __(' (required)', 'shcreate');
		} else {
			$req_text = '';
		}
		$aria_req = ( $req ? " aria-required='true'" : '' );

		$comment_args =  array(
			'fields' => apply_filters( 'comment_form_default_fields', array(
				'url' => '<div class="comment-form-url">'
                    . '<input id="url" name="url" type="text" placeholder="' . __('Website', 'shcreate') 
                    . '" value="' . esc_attr( $commenter['comment_author_url'] ) 
                    . '" /></div>',

				'author' => '<div class="comment-form-author">'
					. '<input id="author" name="author" type="text" placeholder="' . __('Name', 'shcreate') . $req_text 
					. '" value="' . esc_attr( $commenter['comment_author'] ) 
					. '"' . $aria_req . ' /></div>',

  				'email' => '<div class="comment-form-email">' 
    				. '<input id="email" name="email" type="text" placeholder="' . __('Email', 'shcreate') . $req_text 
					. '" value="' . esc_attr(  $commenter['comment_author_email'] ) 
    				. '"' . $aria_req . ' /></div>',

			)),

			'comment_notes_before' => '<p class="comment-notes">' 
				. __( 'Your email address will not be published.', 'shcreate' )  . '</p>',

			'comment_field' => '<p class="comment-form-comment">'
    			. '<textarea id="comment" name="comment" cols="60" placeholder="' 
				. __('Enter your Comments', 'shcreate') 
				. '" rows="6" aria-required="true"></textarea></p>',

			'label_submit' => __('Post Your Comment', 'shcreate')
		);

		add_filter("comment_id_fields", array($this, "my_submit_comment_message") );

		comment_form($comment_args);
	}

	/*
	 * Comment Filter
	 */
	public function my_submit_comment_message($result){
    	return $result.' <div class="reply-error-holder hidden">Error Div</div>';
    }

	/*
	 * Displaying the comment list
	 */
	public function commentList($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		} ?>
		<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
			<div class="comment-avatar">
				<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
			</div>
		<?php endif; ?>
			<div class="comment-container">
				<div class="comment-heading">
						<span class="reply">	
        					<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                		</span>

						<?php printf(__('<cite class="fn accent">%s</cite>', 'shcreate'), get_comment_author_link()) ?>
						<?php if ($comment->comment_approved == '0') : ?>
						<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'shcreate') ?></em>
						<br />
						<?php endif; ?>

						<span class="comment-meta commentmetadata">
							<?php
							/* translators: 1: date, 2: time */
							printf( __('%1$s at %2$s', 'shcreate'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)', 'shcreate'),'  ','' );
							?>
						</span>
				</div>
				<?php comment_text() ?>
			</div>

		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
<?php
        }
}
