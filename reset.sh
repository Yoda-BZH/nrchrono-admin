#!/bin/bash

supervisorctl stop all
app/console doctrine:schema:drop --force --env=prod
app/console doctrine:schema:create --env=prod
app/console faker:fixtures 12 --env=prod
supervisorctl start all
