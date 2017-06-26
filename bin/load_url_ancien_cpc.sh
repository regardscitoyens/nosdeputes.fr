#!/bin/bash

. $(dirname $0)/db.inc

echo "select slug,  slug from parlementaire WHERE anciens_mandats LIKE '%2012 / nomination%'  OR anciens_mandats LIKE '%2014 / nomination%' OR anciens_mandats LIKE '%2014 / reprise%' OR anciens_mandats LIKE '%2015 / reprise%' OR anciens_mandats LIKE '%2016 / nomination%' OR anciens_mandats LIKE '%2017 / fin%' OR anciens_mandats LIKE '%2017 / nomination%' OR anciens_mandats LIKE '%2017 / reprise%' ; " | 
	mysql $MYSQLID $DBNAME | 
	sed 's/-becot//' | #cas de Yannick Favennec qui a changé de nom
	sed 's|^|https://2012-2017.nosdeputes.fr/|'  | 
	awk '{print "update parlementaire set url_ancien_cpc = \""$1"\" where slug = \""$2"\";"}' | mysql $MYSQLID $DBNAME
