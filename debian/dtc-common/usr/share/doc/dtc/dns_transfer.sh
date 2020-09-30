#!/bin/sh

# This peace of script helps you to transfer a list of domains
# that is on a DTC panel, to have a backup DNS not running DTC act as backup NS
# for a DTC panel enabled server

# Edit this to the site running you want to get the list of domains:

SITE_URL=dtc.your-site.com
LOGIN="xxx"
PASS="yyy"
BACKUP_FILE="/var/slave_dns.conf"

# Don't edit those, should be allright as default:
WGET_TOOL=links

URL="http://${SITE_URL}/dtc/list_domains.php?action=list_dns&server_login=${LOGIN}&server_pass=${PASS}"

${WGET_TOOL} -source "${URL}" >${BACKUP_FILE}

killall -HUP named

