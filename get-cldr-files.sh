#!/bin/bash

npm install cldr-core
npm install cldr-localenames-modern
npm install cldr-numbers-modern
rm -rf json/v28.0.2
mkdir json/v28.0.2
mv ../node_modules/cldr-*/ json/v28.0.2/
