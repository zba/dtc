<?php

// This is the config file for your Tucows account.
// Please enter your username and keys there...
// once it's done, copy this file with name "srs_config.php".
// This done like that so when you upgrade to newer version
// of DTC, this file doen't overight your working config file.
//
// Thomas GOIRAND <thomas [ at ] goirand.fr>

class openSRS extends openSRS_base {
//        var $USERNAME         = 'YOUR_USER_NAME_HERE';            # your OpenSRS username
//        var $TEST_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the test (horizon) server
//        var $LIVE_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the live server
//        var $environment      = 'TEST';              # 'TEST' or 'LIVE'
//        var $crypt_type       = 'DES';               # 'DES' or 'BLOWFISH';
//        var $ext_version      = 'Foobar';            # anything you want

        var $USERNAME         = $_GLOBAL["conf_srs_user"];      # your OpenSRS username
        var $TEST_PRIVATE_KEY = $_GLOBAL["conf_srs_test_key"];  # your private key on the test (horizon) server
        var $LIVE_PRIVATE_KEY = $_GLOBAL["conf_srs_live_key"];  # your private key on the live server
        var $environment      = $_GLOBAL["conf_srs_enviro"];              # 'TEST' or 'LIVE'
        var $crypt_type       = $_GLOBAL["conf_srs_crypt"];               # 'DES' or 'BLOWFISH';
        var $ext_version      = $_GLOBAL["conf_dtc_version"];            # anything you want
}

?>
