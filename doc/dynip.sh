#!/bin/sh

LYNX=/usr/bin/lynx
DOMAIN=your-domain-name.com
LOGIN=xxxxxx
PASS=XXXXXX
SCRIPT_URL="https://dtc.YOUR-HOST.com/dtc/"

$LYNX -source $SCRIPT_URL"dynip.php?login="$LOGIN"&pass="$PASS"&domain="$DOMAIN
