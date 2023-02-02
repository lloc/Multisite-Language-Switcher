#!/usr/bin/env sh

set -e

npm run docs:build
docker run --rm -v "$(pwd):/data" "phpdoc/phpdoc:3"

cd docs/.vuepress/dist
echo 'msls.co' > CNAME
cd -
