#!/bin/bash

for file in wget/* ; do
    perl cut_quest.pl $file 1 > $(echo $file | sed 's/html/input/'  | sed 's/wget/input/'| sed 's/htm/xml/')
done
