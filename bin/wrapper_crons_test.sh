#!/bin/bash

command=`echo $1 | sed 's#^.*\/\([^\/]\+\)$#\\1#'`
tmpout=cron-$command-`date +%d%m%Y-%HH%Mm%S`
tmpoutput=$tmpout

echo $tmpout
