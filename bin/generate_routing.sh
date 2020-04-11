#!/bin/bash
. bin/db.inc
cat apps/frontend/config/routing.yml.example | sed "s/%LEGISLATURE%/$LEGISLATURE/" | sed 's|//|/|g' > apps/frontend/config/routing.yml
