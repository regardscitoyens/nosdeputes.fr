#!/bin/bash

. bin/db.inc

command=`echo $2 | sed 's#^.*\/\([^\/]\+\)$#\\1#'`
tmpout=cron-$command-`date +%d%m%Y-%HH%Mm%S`
tmpoutput=$tmpout
ct=0
while [ -f /tmp/$tmpoutput ]; do
  tmpoutput=$tmpout-$ct
  ct=$(($ct + 1))
done

"$@" >> /tmp/$tmpoutput 2>&1

if test "$PADBOT" && test "$PADURL" ; then
date > /tmp/$tmpoutput.date
cat /tmp/$tmpoutput.date /tmp/$tmpoutput | grep -v '^==' | $PADBOT $PADURL write
rm /tmp/$tmpoutput.date
fi

cat /tmp/$tmpoutput
outsize=`ls -l /tmp/$tmpoutput | tail -n 1 | awk '{print $5}'`
if [ "$outsize" -eq 0 ]; then
  rm -f /tmp/$tmpoutput
fi

