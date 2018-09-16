#!/bin/bash

case "$1" in
  "ns_local")
    ansible-playbook \
    -i inventories/local \
    -b books/local_install.yml \
    -e dont_touch_my_docker=true \
    -e use_stretch=true \
    -e cpc_senat=true \
    -e cpc_repo=/home/njoyard/dev/rc/nossenateurs.fr \
    -e cpc_version=nossenateurs.fr_foransible \
    -e cpc_force_git=true \
    -e cpc_dump=/home/njoyard/dev/rc/nossenateurs.fr_donnees.sql.gz
    ;;

  "ns_cocolulu_dev")
    ansible-playbook \
    -i inventories/dev_ns_cocolulu \
    -b books/remote_install.yml \
    -e dont_touch_my_docker=true \
    -e cpc_senat=true \
    -e cpc_version=nossenateurs.fr_foransible \
    -e cpc_force_git=true \
    -e cpc_dump=/home/njoyard/dev/rc/nossenateurs.fr_donnees.sql.gz
    ;;

  *)
    echo "Usage: $0 <target>"
    echo "Possible targets:"
    echo "  ns_local"
    echo "  ns_cocolulu_dev"
    exit 1
    ;;
esac
