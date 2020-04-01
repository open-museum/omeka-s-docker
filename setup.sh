#!/bin/bash
if [ ! -d db ]
then
    mkdir -p db;
fi
if [ ! -d files ]
then
    mkdir -p files;
fi
if [ ! -d letsencrypt ]
then
    mkdir -p letsencrypt;
fi
if [ ! -f .env ]
then
  rm database.ini;
  export "$(cat .env | xargs)";
fi
echo "user     = \"$MYSQL_USER\"" > database.ini
echo "password = \"$MYSQL_PASSWORD\"" >> database.ini
echo "dbname   = \"$MYSQL_DATABASE\"" >> database.ini
echo "host     = \"omeka-db\"" >> database.ini