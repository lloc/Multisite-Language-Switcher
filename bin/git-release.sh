#!/usr/bin/env bash

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
PLUGIN_NAME="multisite-language-switcher"
BUILD_PATH="$PROJECT_ROOT/$PLUGIN_NAME"
ZIP_ARCHIVE="$PROJECT_ROOT/$PLUGIN_NAME.zip"

rm -f $ZIP_ARCHIVE
rm -rf $BUILD_PATH && mkdir $BUILD_PATH

rsync -arvp --exclude-from=$PROJECT_ROOT/.distignore $PROJECT_ROOT/ $BUILD_PATH/
cd $PROJECT_ROOT && zip -r $ZIP_ARCHIVE $PLUGIN_NAME
