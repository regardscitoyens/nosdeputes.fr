#!/usr/bin/env python
# -*- coding: utf-8 -*-

from twitter import Twitter, OAuth
from pprint import pprint

KEY = 'xxxxxxxxxxxxxxxxxxxxx'
SECRET = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
OAUTH_TOKEN = 'xxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
OAUTH_SECRET = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'

LIST_USER = "AssembleeNat"
LIST_SLUG = "les-députés"
#LIST_USER = "Senat_Info"
#LIST_SLUG = "senateurs"

t = Twitter(auth=OAuth(OAUTH_TOKEN, OAUTH_SECRET, KEY, SECRET))
accounts = {}
page = 1
cursor = -1
while cursor:
    res = t.lists.members(owner_screen_name=LIST_USER, slug=LIST_SLUG, cursor=cursor, include_entities='false', skip_status='true')
    cursor = res['next_cursor']
    new = 0
    for account in res['users']:
        name = account['screen_name'].lower()
        if name not in accounts:
            accounts[name] = account
            new += 1
    print("INFO: page %s -> %s results including %s new ; new total: %s" % (page, len(res['users']), new, len(accounts)))
    page += 1

print accounts
