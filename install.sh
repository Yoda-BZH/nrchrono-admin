#!/bin/bash

composer install

app/console assets:install --symlink web

highcharts="Highcharts-4.1.5.zip"
if [ ! -d web/highcharts/js ]
then
    wget http://code.highcharts.com/zips/$highcharts
    unzip -d web/highcharts $highcharts
fi


countdown="jquery.countdown.package-2.0.2.zip"

if [ ! -d web/jqcountdown ]
then
    wget http://keith-wood.name/zip/$countdown
    unzip -d web/jqcountdown $countdown
fi



if [ ! -d web/js/sprintf ]
then
    git clone https://github.com/alexei/sprintf.js web/js/sprintf
fi

app/console ca:cl --env=dev
app/console ca:cl --env=prod
app/console team:color
app/console assetic:dump --env=prod --no-debug
