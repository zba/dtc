<?php

// This is the config file for your Tucows account.
// Please enter your username and keys there...
// once it's done, copy this file with name "srs_config.php".
// This done like that so when you upgrade to newer version
// of DTC, this file doen't overight your working config file.
//
// Thomas GOIRAND <thomas@goirand.fr>

class openSRS extends openSRS_base {
        var $USERNAME         = 'YOUR_USER_NAME_HERE';            # your OpenSRS username
        var $TEST_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the test (horizon) server
        var $LIVE_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the live server
        var $environment      = 'TEST';              # 'TEST' or 'LIVE'
        var $crypt_type       = 'DES';               # 'DES' or 'BLOWFISH';
        var $ext_version      = 'Foobar';            # anything you want
}

?>