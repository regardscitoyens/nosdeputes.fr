#!/bin/bash

for file in `grep "pas encore édité" */* | sed 's/\.asp:.*$/.asp/'`; do
  rm $file
done;


