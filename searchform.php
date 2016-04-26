<div class="search-form">
	<form method="get" action="<?php echo home_url(); ?>/">
		<span class="search-span">
			<?php $pretext = __('Search', 'shcreate'); ?>
    		<label class="hidden" for="s"><?php echo $pretext; ?></label>
    		<input type="text" value="<?php echo $pretext; ?>" name="s" id="s" 
    		onfocus="if (this.value == '<?php echo $pretext; ?>') {this.value = '';}" 
    		onblur="if (this.value == '') {this.value = '<?php echo $pretext; ?>';}" />
			<i class="search-i fa fa-search"></i>
		</span>
	</form>
</div>
