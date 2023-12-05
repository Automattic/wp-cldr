#!/bin/bash

# confirm current directory is the wp-cldr directory
if [ "wp-cldr" != "${PWD##*/}" ]; then
	echo "Please run this script from within 'wp-cldr' directory"
	exit
fi

# set the CLDR version
CLDRVERSION="45.0.0"

DATA_DIR="$PWD"/data/$CLDRVERSION

# re-create any existing data files for this CLDR version
rm -rf "$DATA_DIR"
mkdir -p "$DATA_DIR"

# download the CLDR JSON reference distribution from GitHub
ZIP_TMP_DIR=$(mktemp -d)
DATA_TMP_DIR=$(mktemp -d)

curl -L -o $ZIP_TMP_DIR/cldr.zip https://github.com/unicode-org/cldr-json/archive/$CLDRVERSION.zip
unzip $ZIP_TMP_DIR/cldr.zip -d $DATA_TMP_DIR

rm -rf $ZIP_TMP_DIR

# copy the license and availableLocales files
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-core/LICENSE "$DATA_DIR"
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-core/availableLocales.json "$DATA_DIR"

# copy the supplemental data JSON directory
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-core/supplemental "$DATA_DIR"/supplemental
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-bcp47/bcp47/timezone.json "$DATA_DIR"/supplemental/timezone.json

# copy the locale JSON directories, merging into a single directory tree
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-numbers-full/main/ "$DATA_DIR"/main
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-dates-full/main/ "$DATA_DIR"/main
cp -av $DATA_TMP_DIR/cldr-json-$CLDRVERSION/cldr-json/cldr-localenames-full/main/ "$DATA_DIR"/main

rm -rf $DATA_TMP_DIR
