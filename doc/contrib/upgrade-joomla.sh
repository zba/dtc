#!/bin/bash

#----------------------------------------------------------------------------------------------
# Usage
# ./upgrade-joomla.sh url last_joomla_version_number new_joomla_version_number
#
# You can also put a file, named templates.zip, with your templates in the Joomla! package dir
#----------------------------------------------------------------------------------------------

# Parameters check
if [ "$1" = "" ]; then 
	echo "\r\n"
	echo "==> You must specify an url ! (You can also type the last & the new Joomla! version to auto fill the dtc-pkg-info.php file)"
	echo "eg: ./upgrade-joomla.sh url last_joomla_version_number new_joomla_version_number"
	echo "\r\n"
	exit
fi

# Creating & moving to working dir
echo "==> Creating temp dir"
mkdir tmp
echo "==> Changing to temp dir"
cd tmp

# File download with 3 retries attempts
echo "==> Downloading renaming & extracting Joomla! archive"
echo "---------------------------------------------------------------"
wget -t 3 "$1"

# If HTML file rename it
if [ ${1##*.} = "html" ]; then
	mv *.html *.zip
fi

# Unzip & remove Joomla! archive
unzip -q *.zip
rm *.zip

# If templates zip file found, extract to working dir
if [ -f ../templates.zip ]; then
	echo "---------------------------------------"
	echo "==> Extracting & Integrating Joomla! Templates"
	unzip -q ../templates.zip -d ./
else
	echo "+++ No templates ZIP file to integrate - skipping section"
	echo "+++ Put a file, named templates.zip, with your templates in the Joomla! package dir"
	echo "+++ Structure inside the ZIP file must be like this: 
	templates.zip	
	 templates (dir)
	 |-> toto_template (dir)
	 |-> tata_template (dir)
	 .... "
fi

# Change owner, compress archive and moving to Joomla! package dir
echo "==> Changing owner to dtc:dtcgrp"
chown dtc:dtcgrp * -R
echo "==> Compressing archive"
tar czf Joomla-latest-stable-fr.tar.gz *
echo "==> Moving to Joomla! package dir"
mv Joomla-latest-stable-fr.tar.gz ../

# If parameters found -> Parsing and replace version in dtc-pkg-info.php
if [ "$2" != "" ] && [ "$3" != "" ]; then
	echo "==> Finding & replacing Joomla! version in dtc-pkg-info.php"
	sed -i 's/'$2'/'$3'/g' ../dtc-pkg-info.php
else
	echo "+++ Version parameters not found - skipping section"
fi

# Displaying total extracted size
echo "==> Total size of installation in bytes : "
echo "---------------------------------------"
du -b -s
echo "---------------------------------------"

# Erasing working dir & files
echo "==> Erasing temp files"
rm * -R
echo "==> Erasing temp dir"
cd ..
rmdir tmp
