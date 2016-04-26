<?php

/*
 * Twitter widget - uses twiteroauth libraries to connect with Twitter 
 * twitteroauth located in admin/twitteroauth
 * source - https://github.com/abraham/twitteroauth
 */

class twitter_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			// Base ID of your widget
			'twitter_widget', 
			// Widget name will appear in UI
			__('SH-Themes Twitter Widget', 'shcreate'), 

			// Widget description
			array( 'description' => __( 'Twitter feed', 'shcreate' ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		global $shcreate;

		$transName = 'tweet_list';
	    $cacheTime = 20;  // Minutes

		$screenName = $instance['screen_name'];
		$consumerKey = $shcreate['twit-consumerkey'];
		$consumerSecret = $shcreate['twit-consumersecret'];
		$accessToken = $shcreate['twit-accesstoken'];
		$accessTokenSec = $shcreate['twit-accesstokensecret'];
		$tweetCount = $instance['num_tweets'];

		$twitterData = $this->get_tweets($screenName, $consumerKey, $consumerSecret, $tweetCount);
		//print_r($twitterData);

		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		?>

		<?php
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is the output
		?>
		<div class="twitter-feed">

		<?php /* Display Latest Tweets */ ?>
		
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

            <?php
            	if(!empty($twitterData) || !isset($twitterData['error'])){
            		$i=0;
					$hyperlinks = true;
					$encode_utf8 = true;
					$twitter_users = true;
					$update = true;

					echo '<ul class="twitter_update_list">';
					
					if (!empty($twitterData)) {
		            	foreach($twitterData as $item){
							$name = isset($item->user->name) ? $item->user->name : '';
							$icon = isset($item->user->profile_image_url_https) ? $item->user->profile_image_url_https : '';
		                    $msg = isset($item->text) ? $item->text : '';

							$item_id_str = isset($item->id_str) ? $item->id_str : '';
		                    $permalink = 'http://twitter.com/#!/'. $instance['screen_name'] .'/status/'. $item_id_str;
		                    if($encode_utf8) $msg = utf8_encode($msg);
                                    $msg = $this->encode_tweet($msg);
		                    $link = $permalink;

		                     echo '<li class="twitter-item">' . '<div class="twit-icon"><img src="' . $icon . '"/></div>'
								 . '<div class="twit-content">';


		                      if ($hyperlinks) {    $msg = $this->hyperlinks($msg); }
		                      if ($twitter_users)  { $msg = $this->twitter_users($msg); }
							  else { $msg = ''; }

		                      echo '<div class="twit-msg">' . $msg . '</div>';

		                    if($update) {
								$created_at = isset($item->created_at) ? $item->created_at : '';
		                      $time = strtotime($created_at);

		                      //if ( ( abs( time() - $time) ) < 86400 )
		                        $h_time = sprintf( __('%s ago', 'shcreate'), human_time_diff( $time ) );
		                      //else
		                      //  $h_time = date(__('Y/m/d'), $time);
							
		                      echo sprintf( __('%s', 'shcreate'),' <div class="twitter-timestamp"><abbr title="' . date(__('Y/m/d H:i:s', 'shcreate'), $time) . '">' . $h_time . '</abbr></div></div>' );
		                     }

		                    echo '</li>';

		                    $i++;
		                    if ( $i >= $instance['num_tweets'] ) break;
		            	}
					}

					echo '</ul>';

					if ($instance['tweettext'] != '') {
						//wpml translate tweettext
						/* Deprecated
						$icl_t = function_exists('icl_t');
						$tweettext = $icl_t ? icl_t('SH-Themes Twitter Widget', 'Tweet Text' . $this->id, $instance['tweettext']) 
							: $instance['tweettext'];
						*/
						$tweettext = apply_filters('wpml_translate_single_string', $instance['tweettext']
							, 'SH-Themes Twitter Widget', 'Tweet Text' . $this->id);
			
						echo '<a href="https://twitter.com/' . $instance['screen_name'] . '"
                    		data-show-count="true"
                    		data-lang="en">' . $tweettext . '</a> ';
					} else {  // default link
						echo __('Follow', 'shcreate') . ' <a href="https://twitter.com/' . $instance['screen_name'] . '"
                		data-show-count="true"
                		data-lang="en">' . $instance['screen_name'] . '</a> ';
					}
            	}
            ?>
		</div>

		<?php
		echo $args['after_widget'];

	}
		
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'shcreate' );
		}
		
		if ( isset( $instance[ 'screen_name' ] )) {
			$screen_name = esc_attr($instance[ 'screen_name' ]);
		} else {
			$screen_name = '';
		}

		if ( isset( $instance[ 'num_tweets' ] )) {
            $num_tweets = esc_attr($instance[ 'num_tweets' ]);
        } else {
            $num_tweets = '';
        }

		if ( isset( $instance[ 'tweettext' ] )) {
			$tweettext = esc_attr($instance[ 'tweettext']);
		} else {
			$tweettext = '';
		}

		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'shcreate' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'screen_name' ); ?>">Screen name:</label>
		<input id="<?php echo $this->get_field_id( 'screen_name' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'screen_name' ); ?>" type="text" value="<?php echo $screen_name; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'num_tweets' ); ?>">Number of Tweets:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'num_tweets' ); ?>" name="<?php echo $this->get_field_name( 'num_tweets' ); ?>" type="text" value="<?php echo $num_tweets; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tweettext' ); ?>"><?php _e('Follow Text e.g. Follow me on Twitter', 'shcreate') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tweettext' ); ?>" name="<?php echo $this->get_field_name( 'tweettext' ); ?>" value="<?php echo $tweettext; ?>" />
		</p>

		<p>
		<?php echo __('<b>Note:</b> Please make sure you set up your Twitter API settings under Theme Options -> API Settings in order to use this widget.  These settings are now required by Twitter', 'shcreate'); ?>
		</p>


		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['screen_name'] = ( ! empty( $new_instance['screen_name'] ) ) ? strip_tags( $new_instance['screen_name'] ) : '';
		$instance['num_tweets'] = ( ! empty( $new_instance['num_tweets'] ) ) ? strip_tags( $new_instance['num_tweets'] ) : '';
		$instance['tweettext'] = ( ! empty( $new_instance['tweettext'] ) ) ? strip_tags( $new_instance['tweettext'] ) : '';

		// WPML
		/* Deprecated
		//wpml translate tweettext
		if (function_exists('icl_register_string')) {
			icl_register_string('SH-Themes Twitter Widget', 'Tweet Text' . $this->id, $instance['tweettext']);
		}
		*/

    	/**
     	 * register strings for translation
     	 */
    	do_action( 'wpml_register_single_string', 'SH-Themes Twitter Widget', 'Tweet Text' . $this->id, $instance['tweettext'] );
    	//WMPL

		return $instance;
	}


	/**
	 * Find links and create the hyperlinks
	 */
	private function hyperlinks($text) {
	    $text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);
	    $text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);

	    // match name@address
	    $text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);

	    //mach #trendingtopics. Props to Michael Voigt
	    $text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text);

	    return $text;
	}

	/**
	 * Find twitter usernames and link to them
	 */
	private function twitter_users($text) {
    	$text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);
	    return $text;
	}

    /**
     * Encode single quotes in your tweets
     */
    private function encode_tweet($text) {
        $text = mb_convert_encoding( $text, "HTML-ENTITIES", "UTF-8");
        return $text;
    }

	/*
	 * Public function get tweets - uses wp_remote instead of former curl methods used in twitteroauth
	 */
	public function get_tweets($screenName, $consumerKey, $consumerSecret, $tweetCount) {
		// accessToken and accessTokenSec are not the same as bearer token

    	// some variables
    	$token = get_option('shTwitterToken');

		$twitter_timeline           = "user_timeline";  //  mentions_timeline / user_timeline / home_timeline / retweets_of_me
        $request = array (
            'screen_name' => $screenName,
            'count' => $tweetCount,
        );
 
    	// get tweet cache from db
		//$shTweetCache = false;  // for testing
		$shTweetCache = get_transient('shTweetCache');
 
    	// cache version does not exist or expired
    	if (false === $shTweetCache) {
        	// getting new auth bearer only if we don't have one
        	if(!$token) {
            	// preparing credentials
            	$credentials = $consumerKey . ':' . $consumerSecret;
            	$toSend = base64_encode($credentials);
 
            	// http post arguments
            	$args = array(
                	'method' => 'POST',
                	'httpversion' => '1.1',
                	'blocking' => true,
                	'headers' => array(
                    	'Authorization' => 'Basic ' . $toSend,
                    	'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                	),
                	'body' => array( 'grant_type' => 'client_credentials' )
            	);
 
            	add_filter('https_ssl_verify', '__return_false');
            	$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);
 
            	$keys = json_decode(wp_remote_retrieve_body($response));
 		
            	if($keys) {
                	// saving token to wp_options table
					if (isset($keys->access_token)) {
                		update_option('shTwitterToken', $keys->access_token);
                		$token = $keys->access_token;
					}
            	}
        	}
        	// we have bearer token wether we obtained it from API or from options
        	$args = array(
            	'httpversion' => '1.1',
            	'blocking' => true,
            	'headers' => array(
                'Authorization' => "Bearer $token"
            	)
        	);
 
        	add_filter('https_ssl_verify', '__return_false');
			// to get followers
   	    	//$api_url = "https://api.twitter.com/1.1/users/show.json?screen_name=$screenName";

			$api_url = "https://api.twitter.com/1.1/statuses/$twitter_timeline.json?". http_build_query($request);
	        $response = wp_remote_get($api_url, $args);
			//echo '<p>' . print_r($response) . '</p>';
 
        	if (!is_wp_error($response)) {
				$shTweetCache = json_decode(wp_remote_retrieve_body($response));
        	} else {
            	// get old value and break
            	$shTweetCache = get_option('shTweetCache');
            	// uncomment below to debug
            	//die($response->get_error_message());
        	}
 
        	// cache for an hour
        	set_transient('shTweetCache', $shTweetCache, 1*60*60); // 1 hour = 1*60*60
        	update_option('shTweetCache', $shTweetCache);
    	}
 
    	//return $numberOfFollowers;
		return $shTweetCache;
	}


} // End Class

// Register and load the widget
function theme_load_twitter() {
	register_widget( 'twitter_widget' );
}
add_action( 'widgets_init', 'theme_load_twitter' );

