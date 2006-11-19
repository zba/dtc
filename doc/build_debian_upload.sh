!#/bin/sh

cvs -d :pserver:anonymous@gplhost.com:/var/lib/cvs export -D today dtc

VERS=`cat dtc/bin/version`
RELS=`cat dtc/bin/release`
VERSION=${VERS}"-"${RELS}

rm -f dtc/admin/*.ttf
rm -f dtc/client/*.ttf
rm -f dtc/email/*.ttf

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
