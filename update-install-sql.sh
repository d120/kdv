#!/bin/sh
mysqldump --no-data --skip-add-drop-table kdv|sed 's/CREATE TABLE/CREATE TABLE IF NOT EXISTS/g' > install.sql

