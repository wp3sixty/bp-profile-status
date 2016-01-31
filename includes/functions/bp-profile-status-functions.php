<?php
/*
 * Checking if SCRIPT_DEBUG constant is defined or not
 */

function bpps_get_script_style_suffix() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && constant( 'SCRIPT_DEBUG' ) === true ) ? '' : '.min';

	return $suffix;
}
