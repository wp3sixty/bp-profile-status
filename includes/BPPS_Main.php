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

    /*
     * Constructor
     */

    public function __construct() {
        $this->bpps_load_translation();

        add_action( 'plugins_loaded', array( $this, 'bpps_class_construct' ) );
    }

    /*
     * Loading translation
     */

    public function bpps_load_translation() {
        load_plugin_textdomain( 'bp-profile-status', false, basename( BPPS_PATH ) . '/languages/' );
    }

    /*
     * initializing classes
     */

    public function bpps_class_construct() {
        new BPPS_Profile_Status();
    }

}
