#!/bin/sh
# generate mailfilter stuff
# gen_mailfilter.sh <home> <id> <domain_full_name> <redirection> <redirection2>
home=$1
id=$2
domain_full_name=$3
redirection=$4
redirection2=$5

MAILFILTER_FILE=$home/.mailfilter

# if the file exists, we need to edit and make sure our CC lines are present
if [ -f $MAILFILTER_FILE ]; then

	# first, strip off any additions by DTC
	if grep "Configured by DTC" $MAILFILTER_FILE >/dev/null 2>&1
	then
		start_line=`grep -n "Configured by DTC" $MAILFILTER_FILE | cut -d":" -f1`
		end_line=`grep -n "End of DTC configuration" $MAILFILTER_FILE| cut -d":" -f1`
		nbr_line=`cat $MAILFILTER_FILE | wc -l`
		TMP_FILE=$home/.DTC_uninstall.mailfilter.XXXXXX
		touch $TMP_FILE
		cat $MAILFILTER_FILE | head -n $(( $start_line - 1 )) > $TMP_FILE
		cat $MAILFILTER_FILE | tail -n $(( $nbr_line - $end_line )) >> $TMP_FILE
		cp -f $MAILFILTER_FILE $MAILFILTER_FILE.DTC.removed
		echo -n > $MAILFILTER_FILE
		cat < $TMP_FILE >> $MAILFILTER_FILE
		rm $TMP_FILE
	fi
fi

# create the file, and populate with normal things :)
touch $MAILFILTER_FILE

TMP_FILE=$home/.DTC_install.mailfilter.XXXXXX

touch $TMP_FILE

if [ -z $redirection2 ]; then
	# only do one redirection
echo "# Configured by DTC
# If the destination maildir doesn't exist, create it.
\`[ -d \$DEFAULT ] || (maildirmake \$DEFAULT && maildirmake -f SPAM \$DEFAULT)\`
cc \"! $redirection\"
to \$DEFAULT
# End of DTC configuration" >> $TMP_FILE

else
	# do both redirections from the command line
echo "# Configured by DTC
# If the destination maildir doesn't exist, create it.
\`[ -d \$DEFAULT ] || (maildirmake \$DEFAULT && maildirmake -f SPAM \$DEFAULT)\`
cc \"! $redirection\"
cc \"! $redirection2\"
to \$DEFAULT
# End of DTC configuration" >> $TMP_FILE

fi

# now that we have our temp file with the additions, prepend to the current mailfilter
cat $MAILFILTER_FILE >> $TMP_FILE
# now move the TMP_FILE over top of our MAILFILTER_FILE
mv $TMP_FILE $MAILFILTER_FILE
chmod 500 $MAILFILTER_FILE
chown nobody:65534 $MAILFILTER_FILE
