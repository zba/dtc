#!/bin/sh

cvs -d :pserver:anonymous@gplhost.com:/var/lib/cvs export -D today dtc

VERS=`cat dtc/bin/version`
RELS=`cat dtc/bin/release`
VERSION=${VERS}"-"${RELS}

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
FOUNDED_ARCH=`uname -m`
case "$FOUNDED_ARCH" in
        i386)
                DEBIAN_BINARCH=i386
                ;;
        i436)
                DEBIAN_BINARCH=i386
                ;;
        i586)
                DEBIAN_BINARCH=i386
                ;;
        i686)
                DEBIAN_BINARCH=i386
                ;;
        x86_64)
                DEBIAN_BINARCH=amd64
                ;;
        *)
                echo "Unrecognized arch: exiting!"
                exit 1
                ;;
esac

dupload -c -f --to mentors dtc_${VERSION}_${FOUNDED_ARCH}.changes
