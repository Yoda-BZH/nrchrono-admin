#!/bin/bash

composer install

app/console assets:install --symlink web

highcharts="Highcharts-4.1.3.zip"
if [ ! -f $highcharts ]
then
    wget http://code.highcharts.com/zips/$highcharts
fi

unzip -d web/highcharts $highcharts

countdown="jquery.countdown.package-2.0.2.zip"

if [ ! -f $countdown ]
then
    wget http://keith-wood.name/zip/$countdown
fi

unzip -d web/jqcountdown $countdown

app/console ca:cl --env=dev
app/console ca:cl --env=prod
app/console team:color

