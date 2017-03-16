#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, csv, json
from difflib import SequenceMatcher
from itertools import product, combinations

csv.field_size_limit(sys.maxsize)

def similar(a, b):
    return SequenceMatcher(None, a, b).quick_ratio()

# ----------------------
# Adapted from @ncohen https://github.com/nathanncohen/LobbyTrack/blob/85bb840d97d11d82ff873e11c13505e0b38fe96f/highlightTrack.py
def longest_common_subsequence(list1, list2, i1, i2):
    r"""
    Assuming that list1[i1] == list2[i2], this function extends the matching as
    far as possible to the left and to the right.
    Formally, the function returns the largest j,k such that :
        list1[i1-j:i1+k] ~= list2[i2-j:i2+k]
    """
    k=0
    for k in range(min(len(list1)-i1,len(list2)-i2)):
        if similar(list1[i1+k], list2[i2+k]) < 0.6:
            break
    j=0
    for j in range(min(i1,i2)+1):
        if similar(list1[i1-j], list2[i2-j]) < 0.6:
            break
    return (-j+1,k)

def arr_to_pos(arr):
    r"""
    Returns a dictionary associating to each element of set(arr) the
    sequence of its positions in arr
    """
    d = {}
    for i,x in enumerate(arr):
        if x not in d:
            d[x] = []
        d[x].append(i)
    return d

def find_matchings(arr1, arr2, ngram=3):
    pos1 = arr_to_pos(arr1)
    pos2 = arr_to_pos(arr2)
    matchings = []
    for w, p1 in pos1.items(): # For any word w in mots1
        p2 = pos2.get(w, [])
        for i1, i2 in product(p1, p2): # For any i1,i2 such that mots1[i1]==mots2[i2]
            j, k = longest_common_subsequence(arr1, arr2, i1, i2)
            if k-j >= ngram:
                matchings.append((i1+j, i2+j, k-j))
    return sorted(set(matchings), key=lambda (x, y, z): x) # contains the (i,j,k) such that mots1[i:i+k]==mots2[j:j+k]
# ----------------------

reunions = {}
with open(sys.argv[1]) as f:
    for row in csv.DictReader(f, delimiter="\t"):
        row["id"] = int(row["id"])
        row["seance_id"] = int(row["seance_id"])
        if row["seance_id"] not in reunions:
            reunions[row["seance_id"]] = {
              "ids": [],
              "txts": [],
              "intervs": []
            }
        reunions[row["seance_id"]]["ids"].append(row["id"])
        reunions[row["seance_id"]]["txts"].append(row["intervention"])
        reunions[row["seance_id"]]["intervs"].append(row["parlementaire_id"])

ndurl = lambda i: "https://www.nosdeputes.fr/14/seance/%s" % i

found = False
for s1, s2 in combinations(reunions.keys(), 2):
    res = find_matchings(reunions[s1]["txts"], reunions[s2]["txts"])
    if res:
        print " -> FOUND MATCH!", ndurl(s1), ndurl(s2), len(reunions[s1]["intervs"]), len(reunions[s2]["intervs"]), res
        found = True

exit(int(not found))
