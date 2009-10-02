#!/bin/sh -ev
rm -rf ../../web/images/circonscriptions \
../../apps/frontend/modules/parlementaire/templates/circonscriptions php
cp -r png ../../web/images/circonscriptions
cp -r html php
sed -i 's/circonscription?search=\(......\)/<?php echo url_for("@list_parlementaires_circo?search=\1");?>/' php/*
cp -r php ../../apps/frontend/modules/parlementaire/templates/circonscriptions
