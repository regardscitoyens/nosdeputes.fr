#!/usr/bin/env python

import os, sys, json

split = False
splitval = False
if len(sys.argv) > 1:
    field = sys.argv[1]
    if len(sys.argv) > 2:
        split = True
        if len(sys.argv) > 3:
            splitval = int(sys.argv[3])
else:
    field = "all"

values = {}
def add_value(val):
    if split and ' / ' in val:
        for i,v in enumerate(val.split(' / ')):
            if type(splitval) != int or splitval == i:
                add_value(v)
        return
    if val not in values:
        values[val] = 0
    values[val] += 1

MISSING = []
for dep in os.listdir('json'):
    with open(os.path.join('json', dep)) as f:
        data = json.load(f)
        if field == "all":
            for k in data:
                if data[k] and (type(data[k]) != list or data[k] != [""]):
                    add_value(k)
            continue
        if field in data:
            if type(data[field]) == list:
                if data[field] == [""]:
                    MISSING.append(data["id_institution"])
                for i in data[field]:
                    if i:
                        add_value(i)
            else: add_value(data[field])
        else: MISSING.append(data["id_institution"])

miss = len(MISSING)
if miss <= 3 and max(values.values()) == 1:
    print "ALL UNIQUE FIELDS (", len(values), ")"
    sys.exit(0)

if miss > 3:
    print miss, "MISSING:", MISSING

order = sorted(values, key=lambda x: values[x])
order.reverse()
for k in order:
    print k.encode('utf-8'), ":", values[k]
