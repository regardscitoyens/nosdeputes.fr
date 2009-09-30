#!/bin/sh -ev
rm -rf ../../web/images/circonscriptions \
../../apps/frontend/modules/parlementaire/templates/circonscriptions
cp -r png ../../web/images/circonscriptions
cp -r html ../../apps/frontend/modules/parlementaire/templates/circonscriptions

