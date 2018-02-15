#!/bin/bash

mysqldump -uroot -pmalunki malunki >> /var/sql-dumps/malunki-$(date -d "today" +"%Y%m%d%H%M").sql
