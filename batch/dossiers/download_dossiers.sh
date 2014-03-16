#!/bin/bash
mkdir -p html
cd html
while read url ; do 
	file=$(echo $url | sed 's|.*/||')
	if ! test -e "$file" ; then
		if wget -q -O "$file".tmp $url ; then
			mv "$file".tmp "$file"
			echo "html/$file";
		fi
        fi
done
