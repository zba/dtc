PWD=pwd
VERS=`cat bin/version`
RELS=`cat bin/release`
VERSION=$(VERS)"-"$(RELS)

default:
	@-echo "Building... not..."
clean:	
	if [ -n ""$(DESTDIR) ]; then 	(rm -rf $(DESTDIR)/usr/share/dtc/doc $(DESTDIR)/usr/share/dtc/admin $(DESTDIR)/usr/share/dtc/client $(DESTDIR)/usr/share/dtc/shared $(DESTDIR)/usr/share/dtc/email) ;	fi
	@-echo "OK, clean :)"
install:

	@-echo "Copying to $(DESTDIR)..."

	@-mkdir -p $(DESTDIR)/usr/share/dtc/etc/zones
	@-mkdir -p $(DESTDIR)/usr/share/doc/dtc

	cp -rf doc admin client shared email $(DESTDIR)/usr/share/dtc
	rm -rf $(DESTDIR)/usr/share/dtc/shared/package-installer
	rm -rf $(DESTDIR)/usr/share/dtc/doc/LICENSE

	if ! [ -e $(DESTDIR)/usr/share/dtc/doc ] ;	then 		ln -s /usr/share/doc/dtc $(DESTDIR)/usr/share/dtc/doc ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/admin/gfx ] ; 	then 		ln -s ../shared/gfx $(DESTDIR)/usr/share/dtc/admin/gfx  ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/email/gfx ]  ;	then 		ln -s ../shared/gfx $(DESTDIR)/usr/share/dtc/email/gfx  ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/client/gfx ]  ;	then 		ln -s ../shared/gfx $(DESTDIR)/usr/share/dtc/client/gfx  ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/admin/imgcache ]  ;	then 		ln -s ../shared/imgcache $(DESTDIR)/usr/share/dtc/admin/imgcache  ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/client/imgcache ] ;	then 		ln -s ../shared/imgcache $(DESTDIR)/usr/share/dtc/client/imgcache ;	fi
	if ! [ -e $(DESTDIR)/usr/share/dtc/email/imgcache ] ;	then 		ln -s ../shared/imgcache $(DESTDIR)/usr/share/dtc/email/imgcache ;	fi

	echo "<?php \$$conf_dtc_version=\""$(VERS)"\"; \$$conf_dtc_release=\""$(RELS)"\"; \$$conf_unix_type=\""debian"\"; ?>" > $(DESTDIR)/usr/share/dtc/shared/dtc_version.php
	mkdir -p $(DESTDIR)/usr/share/dtc/shared/imgcache
	cp bin/sources/dtc_db.php bin/sources/restor_db.php $(DESTDIR)/usr/share/dtc/admin

	rm -rf `find $(DESTDIR) -type d -name CVS`
	rm -rf `find $(DESTDIR) -type f -name '*~'`
# Remove those files because of license problems
	rm -rf $(DESTDIR)/usr/share/dtc/admin/inc/arial.ttf
	rm -rf $(DESTDIR)/usr/share/dtc/admin/inc/verdana.ttf
	rm -rf $(DESTDIR)/usr/share/dtc/admin/inc/ukai.ttf

	rm -rf $(DESTDIR)/usr/share/dtc/client/inc/arial.ttf
	rm -rf $(DESTDIR)/usr/share/dtc/client/inc/verdana.ttf
	rm -rf $(DESTDIR)/usr/share/dtc/client/inc/ukai.ttf

	chown -R root:root $(DESTDIR)/usr/share/dtc
	chown -R nobody:65534 $(DESTDIR)/usr/share/dtc/client/imgcache $(DESTDIR)/usr/share/dtc/admin/imgcache $(DESTDIR)/usr/share/dtc/shared/imgcache $(DESTDIR)/usr/share/dtc/email/imgcache
	chown -R nobody:65534 $(DESTDIR)/usr/share/dtc/client/gfx $(DESTDIR)/usr/share/dtc/admin/gfx $(DESTDIR)/usr/share/dtc/shared/gfx $(DESTDIR)/usr/share/dtc/email/gfx
	gzip -9 $(DESTDIR)/usr/share/dtc/doc/changelog

installpkg:
	@-echo "Copying to $(DESTDIR)..."
	@-mkdir -p $(DESTDIR)/usr/share/dtc/shared
	cp -rf shared/package-installer $(DESTDIR)/usr/share/dtc/shared
	rm -rf `find $(DESTDIR) -type d -name CVS`
	rm -rf `find $(DESTDIR) -type f -name '*~'`
	chown -R root:root $(DESTDIR)/usr/share/dtc
#	find $(DESTDIR)/ -iname 'CVS' -exec rm -rf {} \\; &>/dev/null
#	find $(DESTDIR)/ -iname '*~' -exec rm -rf {} \\; &>/dev/null
