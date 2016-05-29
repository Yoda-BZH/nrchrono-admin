#!/bin/bash

sudo supervisorctl stop all
app/console doctrine:schema:drop --force --env=prod
app/console doctrine:schema:create --env=prod
app/console faker:fixtures 3 --env=dev -v
app/console faker:matsport:gen --env=prod
sudo supervisorctl start all
