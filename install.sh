#!/bin/bash

composer install

app/console assets:install --symlink web

highcharts="Highcharts-4.1.3.zip"
if [ ! -d web/highcharts ]
then
    wget http://code.highcharts.com/zips/$highcharts
fi

unzip -d web/highcharts $highcharts

countdown="jquery.countdown.package-2.0.2.zip"

if [ ! -d web/jqcountdown ]
then
    wget http://keith-wood.name/zip/$countdown
fi

unzip -d web/jqcountdown $countdown


if [ ! -d web/js/sprintf ]
then
    git clone https://github.com/alexei/sprintf.js web/js/sprintf
fi

app/console ca:cl --env=dev
app/console ca:cl --env=prod
app/console team:color

