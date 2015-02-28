#!/bin/bash


composer install

app/console assets:install --symlink web

wget http://code.highcharts.com/zips/Highcharts-4.1.3.zip

unzip -d web/highcharts Highcharts-4.1.3.zip
