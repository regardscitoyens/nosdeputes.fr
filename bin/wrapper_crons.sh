#!/bin/bash

command=`echo $2 | sed 's#^.*\/\([^\/]\+\)$#\\1#'`
tmpout=cron-$command-`date +%d%m%Y-%HH%Mm%S`
tmpoutput=$tmpout
ct=0
while [ -f /tmp/$tmpoutput ]; do
  tmpoutput=$tmpout-$ct
  ct=$(($ct + 1))
done

"$@" >> /tmp/$tmpoutput 2>&1

cat /tmp/$tmpoutput
outsize=`ls -l /tmp/$tmpoutput | tail -n 1 | awk '{print $5}'`
if [ "$outsize" -eq 0 ]; then
  rm -f /tmp/$tmpoutput
fi

