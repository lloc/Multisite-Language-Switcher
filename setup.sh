#!/usr/bin/env bash

/usr/bin/curl -s http://getcomposer.org/installer | /usr/bin/php
/usr/bin/php composer.phar install

read -p 'DB User: ' uservar
read -sp 'DB Pass: ' passvar

mysql -u ${uservar} --password=${passvar} --execute="DROP DATABASE IF EXISTS wptest;"
bash bin/install-wp-tests.sh wptest ${uservar} ${passvar} localhost latest
phpunit
