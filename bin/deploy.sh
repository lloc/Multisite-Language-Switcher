#!/usr/bin/env sh

set -e

npm run docs:build
docker run --rm -v "$(pwd):/data" "phpdoc/phpdoc:3"

cd docs/.vuepress/dist
echo 'msls.co' > CNAME

git init
git add -A
git commit -m 'deploy'
git push -f git@github.com:lloc/Multisite-Language-Switcher.git master:gh-pages

cd -
