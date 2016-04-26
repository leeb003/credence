<?php

if ( !isset( $shcreate ) && file_exists( dirname( __FILE__ ) . '/theme/option-config.php' ) ) {
	require_once( get_template_directory() . '/theme/option-config.php' );
}
