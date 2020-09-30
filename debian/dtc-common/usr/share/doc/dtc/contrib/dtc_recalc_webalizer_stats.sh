#!/bin/bash

# Recalculate webalizer stats from scratch using the current DTC logs in each folders
# Copyright Thomas Goirand <thomas@goirand.fr>, released under LGPL like the rest of
# the control panel.

# Usage: cd in /var/www/sites (or wherever you have some user accounts),
# then just call this script!

recalc_stats (){
	touch plop

	for i in accesslog.${SUBDOMAIN}.${DOMAIN_NAME}_*.gz ; do
		if [ -f "$i" ] ; then
			echo "-> Unpacking "$i
			gzip -d $i

			# Calculate the sting without the .gz extension
			FNAME_SIZE=${#i}
			NEW_FNAME_SIZE=$(( ${FNAME_SIZE} - 3 ))
			FNAME=${i:0:${NEW_FNAME_SIZE}}

			echo "-> Merging "$i
			cat ${FNAME} >>plop

			echo "-> Repacking "$FNAME
			gzip $FNAME
		fi
	done

	YEAR=`date +%Y`
	MONTH=`date +%m`
	for i in ${YEAR}/${MONTH}/* ; do
		if [ -f "$i" ] ; then
			cat $i >>plop
		fi
	done

	rm daily_usage_* hourly_usage_* index.html usage* webalizer.*
	webalizer -p -R 50 -Y -n ${SUBDOMAIN}.${DOMAIN_NAME} -o . plop
	rm plop
	chown -R dtc:dtcgrp *
}


CURDIR=`pwd`
# Process all users of the folder
for i in * ; do
	if [ -d $i ] ; then
		cd $i
		NOWDIR=`pwd`
		# Process all domains
		for j in *.* ; do
			if [ -d $j ] ; then
				cd $j/subdomains
				for k in * ; do
					if [ -d "$k/logs" ] ; then
						DOMDIR=`pwd`
						cd "$k/logs"
						DOMAIN_NAME=${j}
						SUBDOMAIN=${k}
						echo "===> Recalculating logs for: "${SUBDOMAIN}.${DOMAIN_NAME}
						recalc_stats
						cd ${DOMDIR}
					fi
				done
				cd $NOWDIR
			fi
		done
		cd $CURDIR
	fi
done
