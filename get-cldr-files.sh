#!/bin/bash

# confirm current directory is the wp-cldr directory
if [ "wp-cldr" != "${PWD##*/}" ]; then
	echo "Please run this script from within 'wp-cldr' directory"
	exit
fi

# set the CLDR version
CLDRVERSION="36.0.0"

DATA_DIR=$PWD/data/$CLDRVERSION

# re-create any existing data files for this CLDR version
rm -rf $DATA_DIR
mkdir -p $DATA_DIR

# download the CLDR JSON reference distribution from GitHub
ZIP_TMP_DIR=$(mktemp -d)
DATA_TMP_DIR=$(mktemp -d)

curl -L -o $ZIP_TMP_DIR/core.zip https://github.com/unicode-cldr/cldr-core/archive/$CLDRVERSION.zip
unzip $ZIP_TMP_DIR/core.zip -d $DATA_TMP_DIR

curl -L -o $ZIP_TMP_DIR/localenames.zip https://github.com/unicode-cldr/cldr-localenames-full/archive/$CLDRVERSION.zip
unzip $ZIP_TMP_DIR/localenames.zip -d $DATA_TMP_DIR

curl -L -o $ZIP_TMP_DIR/numbers.zip https://github.com/unicode-cldr/cldr-numbers-full/archive/$CLDRVERSION.zip
unzip $ZIP_TMP_DIR/numbers.zip -d $DATA_TMP_DIR

curl -L -o $ZIP_TMP_DIR/dates.zip https://github.com/unicode-cldr/cldr-dates-full/archive/$CLDRVERSION.zip
unzip $ZIP_TMP_DIR/dates.zip -d $DATA_TMP_DIR

rm -rf $ZIP_TMP_DIR

# copy the license and availableLocales files
cp -av $DATA_TMP_DIR/cldr-core-$CLDRVERSION/LICENSE $DATA_DIR
cp -av $DATA_TMP_DIR/cldr-core-$CLDRVERSION/availableLocales.json $DATA_DIR

# copy the supplemental data JSON directory
cp -av $DATA_TMP_DIR/cldr-core-$CLDRVERSION/supplemental $DATA_DIR/supplemental

# copy the locale JSON directories, merging into a single directory tree
cp -av $DATA_TMP_DIR/cldr-numbers-full-$CLDRVERSION/main/ $DATA_DIR/main
cp -av $DATA_TMP_DIR/cldr-dates-full-$CLDRVERSION/main/ $DATA_DIR/main
cp -av $DATA_TMP_DIR/cldr-localenames-full-$CLDRVERSION/main/ $DATA_DIR/main

rm -rf $DATA_TMP_DIR
