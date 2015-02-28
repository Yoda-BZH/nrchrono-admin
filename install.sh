#!/bin/bash


composer install

app/console assets:install --symlink web

highcharts="Highcharts-4.1.3.zip"
if [ ! -f $highcharts ]
then
    wget http://code.highcharts.com/zips/$highcharts
fi

unzip -d web/highcharts $highcharts 

app/console ca:cl --env=dev
app/console ca:cl --env=prod
