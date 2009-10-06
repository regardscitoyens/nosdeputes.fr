#!/bin/sh -ev
rm -rf ../../web/images/circonscriptions \
../../web/circonscriptions php
cp -r png ../../web/images/circonscriptions
cp -r html php
sed -i 's/circonscription?search=\(......\)/<?php echo url_for("@redirect_parlementaires_circo?code=\1");?>/' php/*
cp -r php ../../web/circonscriptions
