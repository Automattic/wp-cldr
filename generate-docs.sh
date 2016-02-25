#!/usr/bin/env sh

# Get ApiGen.phar
wget http://www.apigen.org/apigen.phar

# Generate Api
php apigen.phar generate -s class.wp-cldr.php -d ../gh-pages --template-theme bootstrap --base-url https://automattic.github.io/wp-cldr/ --title wp-cldr
cd ../gh-pages

# Set identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Add branch
git init
git remote add origin https://${GH_TOKEN}@github.com/automattic/wp-cldr.git > /dev/null
git checkout -B gh-pages

# Push generated files
git add .
git commit -m "API docs update triggered by master commit"
git push origin gh-pages -fq > /dev/null
