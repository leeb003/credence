<?php
/*-----------------------------------------------------------------------------------*/
/*	Ads Widget Class
/*-----------------------------------------------------------------------------------*/

class SH_Ads_Widget extends WP_Widget { 

	public $defaults;

	public function __construct() {
		parent::__construct(
			'sh_ads_widget', // Base ID
			__( 'SH-Themes Ads Widget', 'shcreate'), // Name
			array( 'description' => __('You can place advertisement links with images here', 'shcreate'), ) // Args
		);

		$this->defaults = array(
                'title' => __('Advertisement', 'shcreate'),
                'size' => 'large',
                'num_per_view' => 1,
                'rotate' => 0,
                'randomize' => 0,
                'ad_width' => '',
                'ad_height' => '',
                'ads' => array()
        );
	}

	public function widget( $args, $instance ) {
		
		$instance = wp_parse_args( (array) $instance, $this->defaults );	
		extract( $args );	
		$title = apply_filters('widget_title', $instance['title'] );
		
		echo $before_widget;	
		if ( !empty($title) ) {
			echo $before_title . $title . $after_title;
		}
		?>
			
		<?php if(!empty($instance['ads'])) : ?>
			
			<?php

				if($instance['randomize']){
					shuffle($instance['ads']);
				}
				if(!$instance['rotate']){
					$instance['ads'] = array_slice($instance['ads'],0,$instance['num_per_view']);
				}
				$show_ind = 0;
				
				if($instance['size'] == 'custom'){
					$ad_size = 'style="width:'.$instance['ad_width'].'px; height:'.$instance['ad_height'].'px;"';
				} else {
					$ad_size = '';
				}

			?>
			
			
			<ul class="sh_adswidget_ul <?php echo $instance['size'];?>">
	     		<?php foreach($instance['ads'] as $ind => $ad) : ?>
	     		<li data-showind="<?php echo $show_ind; ?>"><a href="<?php echo $ad['link'];?>" target="_blank"><img src="<?php echo $ad['img'];?>" <?php echo $ad_size; ?>/></a></li>
	     		<?php 
	     			if( !(($ind+1) % $instance['num_per_view'])){
	     				$show_ind++;
	     			}
	     		?>
	     		<?php endforeach; ?>
	    	</ul>
	    
	    <?php 
	    
	    	if(count($instance['ads']) % $instance['num_per_view']){
	    		$show_ind++;
	    	}
	    
	    ?>
	  
	  	<?php if($instance['rotate']) : 
	   		$widget_id = $this->id;
	  		$slide_func_id = str_replace("-","",$this->id);
	  	 	$li_ind = 'li_ind_'.$slide_func_id;
	  	?>

		  	<script type="text/javascript">
				/* <![CDATA[ */
				var <?php echo $li_ind; ?> = 0;
				(function($) {
				  
				  $(document).ready(function(){
				  	slide_ads_<?php echo $slide_func_id; ?>();
				  });
	   	     
				})(jQuery);
				
				function slide_ads_<?php echo str_replace("-","",$this->id); ?>(){
					
					jQuery("#<?php echo $widget_id; ?> ul li").hide();
					jQuery("#<?php echo $widget_id; ?> ul li[data-showind='"+<?php echo $li_ind; ?>+"']").fadeIn(500);
					<?php echo $li_ind; ?>++;
					
					if(<?php echo $li_ind; ?> > <?php echo ($show_ind - 1);?>){
					 <?php echo $li_ind; ?> = 0;
					}
					
					
					
					//alert(<?php echo $li_ind; ?>);
					
					
				 	setTimeout('slide_ads_<?php echo $slide_func_id; ?>()', 5000);
				}
				/* ]]> */
			</script>
			
	 	<?php endif; ?>
	  
    	<?php endif; ?>

		<?php
		
		echo $after_widget;
	}

	
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['size'] = $new_instance['size'];
		$instance['num_per_view'] = absint($new_instance['num_per_view']);
		$instance['rotate'] = isset($new_instance['rotate']) ? 1 : 0;
		$instance['randomize'] = isset($new_instance['randomize']) ? 1 : 0;
		$instance['ad_width'] = absint($new_instance['ad_width']);
		$instance['ad_height'] = absint($new_instance['ad_height']);
		$instance['ads'] = array();
		
		if(!empty($new_instance['ad_img']) && !empty($new_instance['ad_link'])){
			for($i=0; $i < (count($new_instance['ad_img']) - 1); $i++){
				if(!empty($new_instance['ad_img'][$i]) && !empty($new_instance['ad_link'][$i])){
					$ad = array();
					$ad['img'] = esc_url($new_instance['ad_img'][$i]);
					$ad['link'] = esc_url($new_instance['ad_link'][$i]);
					$instance['ads'][] = $ad;
				}
			}	
		}
		
		return $instance;
	}

	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'shcreate'); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		
		<p>
			<label><?php _e('Ads Size', 'shcreate'); ?>:</label><br/>
			<input type="radio" name="<?php echo $this->get_field_name( 'size' ); ?>" class="sh-ad-size" value="small" <?php checked($instance['size'],'small'); ?>/>
			<label><?php _e('Small (150x150px)', 'shcreate'); ?></label><br/>
			<input type="radio" name="<?php echo $this->get_field_name( 'size' ); ?>" class="sh-ad-size" value="large" <?php checked($instance['size'],'large'); ?>/>
			<label><?php _e('Large (Full Width)', 'shcreate'); ?></label><br/>
			<input type="radio" name="<?php echo $this->get_field_name( 'size' ); ?>" class="sh-ad-size" value="custom" <?php checked($instance['size'],'custom'); ?>/>
			<label><?php _e('Custom', 'shcreate'); ?></label>
		</p>
		<?php 
			$custom_display = $instance['size'] == 'custom' ? 'display:block;' : 'display:none'; 
		?>
		<p style="<?php echo $custom_display; ?>">
			<?php _e('Width', 'shcreate'); ?>: 
			<input id="<?php echo $this->get_field_id( 'ad_width' ); ?>" type="text" name="<?php echo $this->get_field_name( 'ad_width' ); ?>" value="<?php echo absint($instance['ad_width']); ?>" class="small-text" />px
			<?php _e('Height', 'shcreate'); ?>:
			<input id="<?php echo $this->get_field_id( 'ad_height' ); ?>" type="text" name="<?php echo $this->get_field_name( 'ad_height' ); ?>" value="<?php echo absint($instance['ad_height']); ?>" class="small-text" />px
	  </p>
		
	  <h4><?php _e('Options', 'shcreate'); ?>:</h4>
		<p>
			<input id="<?php echo $this->get_field_id( 'rotate' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'rotate' ); ?>" value="1" <?php checked(1,$instance['rotate']);?> />
			<label for="<?php echo $this->get_field_id( 'rotate' ); ?>"><?php _e('Rotate (slide) Ads', 'shcreate'); ?>? </label>
	  </p>
		
		<p>
			<input id="<?php echo $this->get_field_id( 'randomize' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'randomize' ); ?>" value="1" <?php checked(1,$instance['randomize']);?> />
			<label for="<?php echo $this->get_field_id( 'randomize' ); ?>"><?php _e('Randomize Ads', 'shcreate'); ?>? </label>
	  </p>
		  
		<p>
			<label for="<?php echo $this->get_field_id( 'num_per_view' ); ?>"><?php _e('Number of Ads per view', 'shcreate'); ?>: </label>
			<input id="<?php echo $this->get_field_id( 'num_per_view' ); ?>" type="text" name="<?php echo $this->get_field_name( 'num_per_view' ); ?>" value="<?php echo absint($instance['num_per_view']); ?>" class="small-text" />
		  <small class="howto"><?php _e('How many ads to display per page load or slide', 'shcreate'); ?></small>
		</p>
		
		
	  <h4><?php _e('Ads', 'shcreate'); ?>:</h4>
	  <p>
		  <ul class="sh_ads_container">
		  <?php foreach($instance['ads'] as $ad) : ?>
		  	<li style="margin-bottom: 15px;">
					<label><?php _e('Ad Image URL', 'shcreate'); ?>:</label>
					<input type="text" name="<?php echo $this->get_field_name( 'ad_img' ); ?>[]" value="<?php echo $ad['img']; ?>" class="widefat" />
					<label><?php _e('Ad Link', 'shcreate'); ?>:</label>
					<input type="text" name="<?php echo $this->get_field_name( 'ad_link' ); ?>[]" value="<?php echo $ad['link']; ?>" class="widefat" />
			</li>
		  <?php endforeach; ?>
		 </ul>
	  </p>
	  
	  <p>
	  	<a href="#" class="sh_add_ad button"><?php _e('Add New', 'shcreate'); ?></a>
	  </p>
	  
		<div class="sh_ads_clone" style="display:none">
			<label><?php _e('Ad Image URL', 'shcreate'); ?>:</label>
			<input type="text" name="<?php echo $this->get_field_name( 'ad_img' ); ?>[]" class="widefat" />
			<label><?php _e('Ad Link URL', 'shcreate'); ?>:</label>
			<input type="text" name="<?php echo $this->get_field_name( 'ad_link' ); ?>[]" class="widefat" />
	  </div>
	  
	<?php
	}
}

/* Initialize Widget */
if(!function_exists('sh_ads_widget_init')):
	function sh_ads_widget_init() {
		register_widget('SH_Ads_Widget');
	}
endif;

add_action('widgets_init','sh_ads_widget_init');

?>
