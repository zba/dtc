<?php

class openSRS extends openSRS_base {
        var $USERNAME         = 'YOUR_USER_NAME_HERE';            # your OpenSRS username
        var $TEST_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the test (horizon) server
        var $LIVE_PRIVATE_KEY = 'abcdef1234567890';  # your private key on the live server
        var $environment      = 'TEST';              # 'TEST' or 'LIVE'
        var $crypt_type       = 'DES';               # 'DES' or 'BLOWFISH';
        var $ext_version      = 'Foobar';            # anything you want
}

?>