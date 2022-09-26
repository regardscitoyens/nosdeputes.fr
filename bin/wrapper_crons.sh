#!/bin/bash

. $(dirname $0)/db.inc

command=`echo $2 | sed 's#^.*\/\([^\/]\+\)$#\\1#'`
tmpout=cron-$command-$LEGISLATURE-`date +%y%m%d-%HH%Mm%S`
tmpoutput=$tmpout
ct=0
while [ -f $PATH_LOGS_CRON/$tmpoutput ]; do
  tmpoutput=$tmpout-$ct
  ct=$(($ct + 1))
done
sleep 5

"$@" >> $PATH_LOGS_CRON/$tmpoutput 2>&1

if test "$PADBOT" && test "$PADURL" ; then
echo > $PATH_LOGS_CRON/$tmpoutput.date
date >> $PATH_LOGS_CRON/$tmpoutput.date
echo "---------------------" >> $PATH_LOGS_CRON/$tmpoutput.date
echo >> $PATH_LOGS_CRON/$tmpoutput.date
cat $PATH_LOGS_CRON/$tmpoutput.date $PATH_LOGS_CRON/$tmpoutput | grep -v '^==' | $PADBOT $PADURL write
rm $PATH_LOGS_CRON/$tmpoutput.date
fi

cat $PATH_LOGS_CRON/$tmpoutput
outsize=`ls -l $PATH_LOGS_CRON/$tmpoutput | tail -n 1 | awk '{print $5}'`
if [ "$outsize" -eq 0 ]; then
  rm -f $PATH_LOGS_CRON/$tmpoutput
fi

