<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BPPS_Main
 *
 * @author sanket
 */
class BPPS_Main {

	/**
	 * Variable declaration
	 */

	var $bpps_suffix;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'bpps_class_construct' ) );
	}

	/*
     * initializing classes
     */

	public function bpps_class_construct() {
		include_once BPPS_PATH . '/includes/main/class-bpps-main-ajax.php';
		include_once BPPS_PATH . '/includes/main/class-bpps-profile-status.php';
		new BPPS_Profile_Status();
		new BPPS_Main_Ajax();
	}
}
