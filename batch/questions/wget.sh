#!/bin/bash
ct=1
while [ $ct -le 72442 ] ; do
  wget http://questions.assemblee-nationale.fr/q13/13-${ct}QE.htm -O wget/${ct}.htm
  ct=$(( ct + 1 ))
done;

