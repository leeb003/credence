<?php
/*
 * Login Widget - a widget to allow logins
*/

class tabbed_login_Widget extends WP_Widget {

	public function __construct() {
        parent::__construct(
            'tabbed-login-widget', // Base ID
            __( 'SH-Themes Login Widget', 'shcreate'), // Name
            array( 'description' => __('Display Login/Register/LostPassword form', 'shcreate'), ) // Args
        );

    }

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Before widget (defined by themes). */
		echo $before_widget;
?>

	<?php 
		//global $user_ID, 
		global $user_identity,$current_url;
		$current_url='http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];		
		$userdata = wp_get_current_user();
		//get_currentuserinfo(); 
		//if (!$user_ID) 
		if (!$userdata->ID)
	{ ?>

<div id="login-register-password">
	<ul class="tabs_login">
		<li class="active_login"><a href="#login" ><?php _e('Login', 'shcreate'); ?></a></li>
		<?php  if(get_option('users_can_register')) { ?>  
		<li><a href="#register"><?php _e('Register', 'shcreate') ?></a></li>
		<?php }; ?>
		<li><a href="#forgot_password"><?php _e('Forgot', 'shcreate'); ?></a></li>
	</ul>
	<div class="tab_container_login">
		<div id="login" class="tab_content_login">

			<?php 
				$register = isset($_GET['register']) ? $_GET['register'] : ''; 
				$reset = isset($_GET['reset']) ? $_GET['reset'] : ''; 
				if ($register == true) { 
			?>

			<h6><?php _e('Success!', 'shcreate'); ?></h6>
			<p><?php _e('Check your email for the password and then return to log in.', 'shcreate'); ?></p>

			<?php } elseif ($reset == true) { ?>
			
			<h6><?php _e('Success!', 'shcreate'); ?></h6>
			<p><?php _e('Check your email to reset your password.', 'shcreate'); ?></p>

			<?php } else { ?>


			<?php } ?>

			<form method="post" action="<?php echo home_url(); ?>/wp-login.php" class="wp-user-form">
				<div class="username">
					<label for="user_login"><?php _e('Username', 'shcreate'); ?>: </label>
					<input type="text" name="log" value="" size="20" id="user_login" tabindex="11" autocomplete="off" />
				</div>
				<div class="password">
					<label for="user_pass"><?php _e('Password', 'shcreate'); ?>: </label>
					<input type="password" name="pwd" value="" size="20" id="user_pass" tabindex="12" />
				</div>				
				<div class="login_fields">
					<div class="rememberme">
						<label for="rememberme">
							<input type="checkbox" name="rememberme" value="forever" checked="checked" id="rememberme" tabindex="13" /><?php _e(' Remember me', 'shcreate'); ?>
						</label>
					</div>
					<?php do_action('login_form'); ?>
					<input type="submit" name="user-submit" value="<?php _e('Login', 'shcreate'); ?>" tabindex="14" class="user-submit sh-btn" />
					<input type="hidden" name="redirect_to" value="<?php echo $current_url; ?>" />
					<input type="hidden" name="user-cookie" value="1" />
				</div>
			</form>
		</div>
		
		<?php  if(get_option('users_can_register')) { ?>  
		
		<div id="register" class="tab_content_login" style="display:none;">
			<h6><?php _e('Register for this site!', 'shcreate'); ?></h6>
			<p><?php _e('Sign up now for the good stuff.', 'shcreate'); ?></p>
			<form method="post" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" class="wp-user-form">
				<div class="username">
					<label for="user_login"><?php _e('Username', 'shcreate'); ?>: </label>
					<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="101" autocomplete="off" />
				</div>
				<div class="password">
					<label for="user_email"><?php _e('Your Email', 'shcreate'); ?>: </label>
					<input type="text" name="user_email" value="" size="25" id="user_email" tabindex="102" />
				</div>
				<div class="login_fields">
					<?php do_action('register_form'); ?>
					<input type="submit" name="user-submit" value="<?php _e('Sign up!', 'shcreate'); ?>" class="user-submit sh-btn" tabindex="103" />
					<?php $register = isset($_GET['register']) ? $_GET['register'] : ''; 
						if($register == true) { echo '<p>Check your email for the password!</p>'; } ?>
					<input type="hidden" name="redirect_to" value="<?php echo $current_url; ?>?register=true" />
					<input type="hidden" name="user-cookie" value="1" />
				</div>
			</form>
		</div>
		
		<?php }; ?>
		
		<div id="forgot_password" class="tab_content_login" style="display:none;">
			<h6><?php _e('Lost Your Password?', 'shcreate'); ?></h6>
			<p><?php _e('Enter your username or email to reset your password.', 'shcreate'); ?></p>
			<form method="post" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" class="wp-user-form">
				<div class="username">
					<label for="user_login" class="hide"><?php _e('Username or Email', 'shcreate'); ?>: </label>
					<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" autocomplete="off" />
				</div>
				<div class="login_fields">
					<?php do_action('login_form', 'resetpass'); ?>
					<input type="submit" name="user-submit" value="<?php _e('Reset my password', 'shcreate'); ?>" class="user-submit sh-btn" tabindex="1002" />
					<?php $reset = isset($_GET['reset']) ? $_GET['reset'] : ''; if($reset == true) { 
						echo '<p>'.__('A message was sent to your email address.','shcreate').'</p>'; 
						} 
					?>
					<input type="hidden" name="redirect_to" value="<?php echo $current_url; ?>?reset=true" />
					<input type="hidden" name="user-cookie" value="1" />
				</div>
			</form>
		</div>
	</div>

	<?php } else { // is logged in ?>

<div id="login-register-password" class="logged-in">
	<div class="sidebox">
		<h6><?php _e('Welcome, ', 'shcreate'); ?> <?php echo $user_identity; ?></h6>
		<?php if (version_compare($GLOBALS['wp_version'], '2.5', '>=')){
			if (get_option('show_avatars')){
		?>
		<div class="usericon">
			<?php 
			//global $userdata; 
			//get_currentuserinfo(); 
			$userdata = wp_get_current_user();
			echo get_avatar($userdata->ID, 50); 
			?>
		</div>
		<?php  }else{?>		
		<style type="text/css">.userinfo p{margin-left: 0px !important;text-align:center;}.userinfo{width:100%;}</style>
		<?php }}?>	
		<div class="userinfo">
			<p><?php _e('You are logged in as ', 'shcreate'); ?> <strong><?php echo $user_identity; ?></strong></p>
			<p>
				<a href="<?php echo wp_logout_url($current_url); ?>"><?php _e('Log out', 'shcreate'); ?></a> | 
				<?php if (current_user_can('manage_options')) { 
					echo '<a href="' . admin_url() . '">' . __('Admin', 'shcreate') . '</a>'; } else { 
					echo '<a href="' . admin_url() . 'profile.php">' . __('Profile', 'shcreate') . '</a>'; } ?>

			</p>
		</div>
	</div>

	<?php } ?>

</div>

<?php
		echo $after_widget;
	}
	
	function form( $instance ) {
	?>
		<p>
			<?php _e('No option available for this widget.', 'shcreate'); ?>
			<br/><strong><?php _e('Note : Do not put the same widget twice in a page.', 'shcreate'); ?></strong>
		</p>

	<?php
	}
}

add_action( 'widgets_init', 'tabbed_load_login_widget',1 );

function tabbed_load_login_widget() {
    $plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
    register_widget( 'tabbed_login_Widget' );
}
