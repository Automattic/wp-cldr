#!/bin/bash

# confirm current directory is the wp-cldr directory
if [ "wp-cldr" != "${PWD##*/}" ]; then
	echo "Please run this script from within 'wp-cldr' directory"
	exit
fi

# set the CLDR version
CLDRVERSION="29.0.0"

# remove any existing data files for this CLDR version
rm -rf data/$CLDRVERSION

# download the CLDR JSON reference distribution from GitHub
wget -O tmp.zip https://github.com/unicode-cldr/cldr-core/archive/$CLDRVERSION.zip && unzip tmp.zip -d temp/ && rm tmp.zip
wget -O tmp.zip https://github.com/unicode-cldr/cldr-localenames-full/archive/$CLDRVERSION.zip && unzip tmp.zip -d temp/ && rm tmp.zip
wget -O tmp.zip https://github.com/unicode-cldr/cldr-numbers-full/archive/$CLDRVERSION.zip && unzip tmp.zip -d temp/ && rm tmp.zip
wget -O tmp.zip https://github.com/unicode-cldr/cldr-dates-full/archive/$CLDRVERSION.zip && unzip tmp.zip -d temp/ && rm tmp.zip

# download latest CLDR XML files
# wget -O tmp.zip http://www.unicode.org/Public/cldr/latest/core.zip && unzip tmp.zip -d temp/ && rm tmp.zip

# confirm the directory exists then copy the license and availableLocales files
mkdir data/$CLDRVERSION
cp -av temp/cldr-core-$CLDRVERSION/LICENSE data/$CLDRVERSION
cp -av temp/cldr-core-$CLDRVERSION/availableLocales.json data/$CLDRVERSION

# copy the supplemental data JSON directory
cp -av temp/cldr-core-$CLDRVERSION/supplemental data/$CLDRVERSION/supplemental

# copy the locale JSON directories, merging into a single directory tree
cp -av temp/cldr-numbers-full-$CLDRVERSION/main/ data/$CLDRVERSION/main
cp -av temp/cldr-dates-full-$CLDRVERSION/main/ data/$CLDRVERSION/main
cp -av temp/cldr-localenames-full-$CLDRVERSION/main/ data/$CLDRVERSION/main

# copy two XML directories (files that aren't available in JSON form yet)
# cp -av temp/common/annotations data/$CLDRVERSION/
# cp -av temp/common/subdivisions data/$CLDRVERSION/

# remove the downloaded originals
rm -rf temp/
