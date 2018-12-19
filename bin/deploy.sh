#!/usr/bin/env sh

set -e

npm run docs:build
phpdoc

cd docs/.vuepress/dist
echo 'msls.co' > CNAME

git init
git add -A
git commit -m 'deploy'
git push -f https://github.com/lloc/Multisite-Language-Switcher.git master:gh-pages

cd -
