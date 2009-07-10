#!/bin/sh

# In case you did a rsync, then rsync once all the content,
# you might want to rsync again all the Mailboxs folders
# in order to make sure all emails remaining are sent to
# the corresponding mailbox once the DNS pointers have been
# changed.
# This script does the job.

#set -e
#set -x

cd /var/www/sites
for i in * ; do
        if [ -d ${i} ] ; then
                NOWDIR=`pwd`
                cd ${i}
                # Process all domains
                for j in *.* ; do
                        if [ -d ${j} ] ; then
                                echo ${i}/${j}/Mailboxs
                                nice rsync -e  ssh -azvp /var/www/sites/$i/$j/Mailboxs/ 66.51.53.4:/var/www/sites/$i/$j/Mailboxs
                        fi
                done
                cd $NOWDIR
        fi
done
