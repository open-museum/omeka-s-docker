#!/bin/bash
if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
  rm database.ini;
  echo "user     = \"$MYSQL_USER\"" > database.ini
  echo "password = \"$MYSQL_PASSWORD\"" >> database.ini
  echo "dbname   = \"$MYSQL_DATABASE\"" >> database.ini
  echo "host     = \"omeka-db\"" >> database.ini
fi
