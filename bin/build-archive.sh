#!/usr/bin/env bash

rm -rf ./build

mkdir ./build
mkdir ./build/meili-search-indexer

cp -R ./inc ./build/meili-search-indexer
cp -R ./vendor ./build/meili-search-indexer
cp meili-search-indexer.php ./build/meili-search-indexer
cp README.md ./build/meili-search-indexer

cd ./build
zip -r meili-search-indexer.zip meili-search-indexer -x "meili-search-indexer/vendor/squizlabs/*"
rm -rf ./meili-search-indexer
cd ..
