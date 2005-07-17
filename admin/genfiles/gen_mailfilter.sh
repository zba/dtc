#!/bin/sh
# generate mailfilter stuff
# gen_mailfilter.sh <home> <id> <domain_full_name> <spam mailbox enable> <spam mailbox> <redirection> <redirection2>
home=$1
id=$2
domain_full_name=$3
spam_mailbox_enable=$4
spam_mailbox=$5
redirection=$6
redirection2=$7

MAILFILTER_FILE=$home/.mailfilter

if [ ! -f $MAILFILTER_FILE ]; then
	touch $MAILFILTER_FILE
fi

# first chown this file, so we can edit it
chmod 660 $MAILFILTER_FILE
chown nobody:65534 $MAILFILTER_FILE

# if the file exists, we need to edit and make sure our CC lines are present
if [ -f $MAILFILTER_FILE ]; then
	COUNT=0
	# first, strip off any additions by DTC
	while grep "Configured by DTC" $MAILFILTER_FILE >/dev/null 2>&1 ; do
		if [ $COUNT -eq 10 ]; then
			echo "Something is wrong with $MAILFILTER_FILE..."
			exit 1;
		fi
		start_line=`grep -n "Configured by DTC" $MAILFILTER_FILE | cut -d":" -f1 | head -n 1`
		end_line=`grep -n "End of DTC configuration" $MAILFILTER_FILE| cut -d":" -f1 | head -n 1`
		nbr_line=`cat $MAILFILTER_FILE | wc -l`
		TMP_FILE=$home/.DTC_uninstall.mailfilter.XXXXXX
		touch $TMP_FILE
		top=$(( $start_line - 1 ))
		bottom=$(( $nbr_line - $end_line ))
		cat $MAILFILTER_FILE | head -n $top > $TMP_FILE
		cat $MAILFILTER_FILE | tail -n $bottom >> $TMP_FILE
		cp -f $MAILFILTER_FILE $MAILFILTER_FILE.DTC.removed
		echo -n > $MAILFILTER_FILE
		cat < $TMP_FILE >> $MAILFILTER_FILE
		rm $TMP_FILE
		COUNT=$(( $COUNT + 1 ))
	done
fi

# create the file, and populate with normal things :)
touch $MAILFILTER_FILE

TMP_FILE=$home/.DTC_install.mailfilter.XXXXXX

touch $TMP_FILE
echo "# Configured by DTC" >> $TMP_FILE

if [ -z $redirection2 ]; then
	if [ -n $redirection ]; then
	# only do one redirection
echo cc \"! $redirection\" " >> $TMP_FILE
	fi
else
	if [ -n $redirection -a -n $redirection2 ]; then
	# do both redirections from the command line
echo "cc \"! $redirection\"
cc \"! $redirection2\" " >> $TMP_FILE
	fi
fi

# we need to put our SPAM catching here, so that other mailboxes don't get it
if [ $spam_mailbox_enable == "yes" ]; then
	echo "
if (/^X-Spam-Flag: .*YES.*/)
{
	\`[ -d \$DEFAULT/.$spam_mailbox ] || (maildirmake \$DEFAULT && maildirmake -f $spam_mailbox  \$DEFAULT)\`
	exception {
		to \$DEFAULT/.$spam_mailbox/
	}
}
" >> $TMP_FILE
fi

echo "# End of DTC configuration" >> $TMP_FILE

# now that we have our temp file with the cc and optionally SPAM additions, append our existing mailfilter to it
cat $MAILFILTER_FILE >> $TMP_FILE

# now we have put our cc at the beginning of the file, put the rest after any user contents
# now onto a default "to"
echo "# Configured by DTC" >> $TMP_FILE
echo "# If the destination maildir doesn't exist, create it.
\`[ -d \$DEFAULT ] || (maildirmake \$DEFAULT && maildirmake -f SPAM \$DEFAULT)\`" >> $TMP_FILE 

# if we have one OR two redirections, we need a default "to"
if [ -n $redirection -o -n $redirection2 ]; then
echo "to \$DEFAULT" >> $TMP_FILE
fi

echo "# End of DTC configuration" >> $TMP_FILE

# now move the TMP_FILE over top of our MAILFILTER_FILE
mv $TMP_FILE $MAILFILTER_FILE
chmod 500 $MAILFILTER_FILE
chown nobody:65534 $MAILFILTER_FILE
