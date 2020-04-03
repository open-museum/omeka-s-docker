#!/bin/bash
if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
  rm -f config/database.ini;
  echo "user     = \"$MYSQL_USER\"" > config/database.ini
  echo "password = \"$MYSQL_PASSWORD\"" >> config/database.ini
  echo "dbname   = \"$MYSQL_DATABASE\"" >> config/database.ini
  echo "host     = \"omeka-db\"" >> config/database.ini
fi
