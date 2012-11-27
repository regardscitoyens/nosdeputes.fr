#!/bin/bash

# HANDLE FILLONISTES BEFORE AN (TEMPO)
cat fillonistes.csv | while read line; do
  depute=`echo $line | awk -F ";" '{print $1}'`
  groupe=`echo $line | awk -F ";" '{print $2}'`
  echo $depute - $groupe
  if [ `grep "$depute" out/* | wc -l` -ne 1 ] ; then
    echo "NOT FOUND"
  else
    file=`grep "$depute" out/* | awk -F ":" '{print $1}'`
    cp $file $file.tmp
    sed 's#"union pour un mouvement populaire / [^"]*"#"'"$groupe"'"#' $file.tmp > $file
    rm $file.tmp
    if [ `grep "Rassemblement" $file | wc -l` -ne 1 ] ; then
      echo "NOT CONVERTED"
    fi
  fi
done
