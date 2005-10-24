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
MAILFILTER_LOCK=$home/.mailfilter.lock

if [ -f $MAILFILTER_LOCK ]; then
	echo "Mailfilter already working on $MAILFILTER_FILE, exitting";
	echo "Two processes trying to work on $MAILFILTER_FILE..." >> /tmp/mailfilter.log
	exit 1;
fi

touch $MAILFILTER_LOCK

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
			echo "Something is wrong with $MAILFILTER_FILE ..."
			echo "Please edit this and manually remove any DTC additions"
			exit 1;
		fi
		start_line=`grep -n "Configured by DTC" $MAILFILTER_FILE | cut -d":" -f1 | head -n 1`
		end_line=`grep -n "End of DTC configuration" $MAILFILTER_FILE| cut -d":" -f1 | head -n 1`
		nbr_line=`cat $MAILFILTER_FILE | wc -l`
		TMP_FILE=$home/.DTC_uninstall.mailfilter.XXXXXX.0
		echo -n > $TMP_FILE
		# if we only have 1 line, and it's the "configured by DTC" one, we need to empty the file
		if [ $nbr_line -eq 1 ]; then
			cat < $TMP_FILE >> $MAILFILTER_FILE
		fi
		top=$(( $start_line - 1 ))
		if [ -z $end_line ]; then
			echo "Something is wrong with $MAILFILTER_FILE ..."
			echo "Please edit this and manually remove any DTC additions"
			exit 1;
		fi

		if [ ""$end_line == " " ]; then
			echo "Something is wrong with $MAILFILTER_FILE ..."
                        echo "Please edit this and manually remove any DTC additions"
                        exit 1;
                fi
		diff=$(( $end_line - $start_line ));
		if [ $diff -gt 15 ]; then
			echo "Something is wrong with $MAILFILTER_FILE..."
			echo "Please edit this and manually remove any DTC additions"
			echo "$id $domain_full_name diff $diff" >> /tmp/mailfilter.log
			echo "$id $domain_full_name broken" >> /tmp/mailfilter.log
			exit 1;
		fi

		bottom=$(( $nbr_line - $end_line ))
		# no point catting 0 lines, now is there ?
		if [ $top -ne 0 ]; then
			cat $MAILFILTER_FILE | head -n $top > $TMP_FILE
		fi
		if [ $bottom -ne 0 ]; then
			cat $MAILFILTER_FILE | tail -n $bottom >> $TMP_FILE
		fi
		echo "$id $domain_full_name $nbr_line $top $bottom" >> /tmp/mailfilter.log
		cp -f $MAILFILTER_FILE $MAILFILTER_FILE.DTC.removed.$COUNT
		echo -n > $MAILFILTER_FILE
		cat < $TMP_FILE >> $MAILFILTER_FILE
		rm $TMP_FILE
		COUNT=$(( $COUNT + 1 ))
	done
fi


# now that we have got rid of the legit additions, try and clean up "bad" End Of Config lines
# this is in case of a bad install, or interupted install
TMP_FILE=$home/.DTC_install.mailfilter.XXXXXX.1
echo -n > $TMP_FILE
grep -v "End of DTC configuration" $MAILFILTER_FILE > $TMP_FILE
# we need to over-write the mail filter file here, not append
cat < $TMP_FILE > $MAILFILTER_FILE
rm $TMP_FILE


# create the file, and populate with normal things :)
touch $MAILFILTER_FILE

TMP_FILE=$home/.DTC_install.mailfilter.XXXXXX.2
echo -n > $TMP_FILE
echo "# Configured by DTC" >> $TMP_FILE

if [ -z ""$redirection2 ]; then
	if [ -n ""$redirection ]; then
	# only do one redirection
echo "cc \"! $redirection\" " >> $TMP_FILE
	fi
else
	if [ -n ""$redirection -a -n ""$redirection2 ]; then
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

# make sure we can have sqwebmail compatible rules, so create the config here
if [ ! -e "$home/Maildir/maildirfilterconfig" ]; then
	maildirmake $home/Maildir
	echo "MAILDIRFILTER=../.mailfilter.sqwebmail
MAILDIR=\$DEFAULT" > $home/Maildir/maildirfilterconfig
fi
chmod 500 $home/Maildir/maildirfilterconfig
chown nobody:65534 $home/Maildir/maildirfilterconfig

# if we have some rules created from sqwebmail, import them here
if [ -e "$home/.mailfilter.sqwebmail" ]; then
	echo "include \".mailfilter.sqwebmail\"" >> $TMP_FILE
fi

echo "# End of DTC configuration" >> $TMP_FILE

# now that we have our temp file with the cc and optionally SPAM additions, append our existing mailfilter to it
cat $MAILFILTER_FILE >> $TMP_FILE


# now we have put our cc at the beginning of the file, put the rest after any user contents
# now onto a default "to"
echo "# Configured by DTC" >> $TMP_FILE
echo "# If the destination maildir doesn't exist, create it.
\`[ -d \$DEFAULT ] || maildirmake \$DEFAULT\`" >> $TMP_FILE 

# if we have one OR two redirections, we need a default "to"
if [ -n ""$redirection -o -n ""$redirection2 ]; then
echo "to \$DEFAULT" >> $TMP_FILE
fi


echo "# End of DTC configuration" >> $TMP_FILE


# now move the TMP_FILE over top of our MAILFILTER_FILE
mv $TMP_FILE $MAILFILTER_FILE
chmod 500 $MAILFILTER_FILE
chown nobody:65534 $MAILFILTER_FILE

rm $MAILFILTER_LOCK
