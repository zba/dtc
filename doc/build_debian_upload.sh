#!/bin/sh

cvs -d :pserver:anonymous@gplhost.com:/var/lib/cvs export -D today dtc

VERS=`cat dtc/bin/version`
RELS=`cat dtc/bin/release`
VERSION=${VERS}"-"${RELS}

rm -f dtc/admin/inc/verdana.ttf
rm -f dtc/admin/inc/arial.ttf
rm -f dtc/admin/inc/ukai.ttf
rm -f dtc/client/inc/verdana.ttf
rm -f dtc/client/inc/arial.ttf
rm -f dtc/client/inc/ukai.ttf

mv dtc dtc-${VERS}
cd dtc-${VERS}/bin
./prepareDebianTree
cd ../..
mv dtc-${VERS}/debian .
tar -cvzf dtc_${VERS}.orig.tar.gz dtc-${VERS}
mv debian dtc-${VERS}
cd dtc-${VERS}
dpkg-buildpackage -rfakeroot -sa
cd ..
dupload -c -f --to mentors dtc_${VERSION}
