#!/usr/bin/env python
# DEPENDANCES 
#  * python-2.5, 
#  * python-html5lib
#  * python-urllib2
# BUGS 
# v0.2
#  * not working with 2006-2007, nor with 2007-2008 (values are shifted)
# v0.1
#  * encoding failures
#  * "objet" text being cut erroneously
#  * not working with 2006-2007, nor with 2007-2008 (values are shifted)

from __future__ import with_statement

import sys
import os
import re

import urllib2

import html5lib
from html5lib import sanitizer
from html5lib import treebuilders

BASE_URL = "http://www.assemblee-nationale.fr/"

# XXX ne marche pas pour 2006-2007 ni pour 2007-2008...
# parce que les positions des td changent
url = "http://www.assemblee-nationale.fr/13/scrutins/table-2008-2009.asp"

#page = urllib2.urlopen("http://www.assemblee-nationale.fr/13/scrutins/table-2007-2008.asp")

# document.findAll("td", {"class":"denom"})[1].parent.findAll('td')
class ScrutinParser:
    def __init__(self, url):
        page = urllib2.urlopen(url)
        parser = html5lib.HTMLParser(tokenizer=sanitizer.HTMLSanitizer, tree=treebuilders.getTreeBuilder("beautifulsoup"))
        self.document = parser.parse(page, encoding="iso8859-15")
        self.type_scrutin = 0

    def parse_scrutin_objet(self, node):
        links = {}
        try:
            links['dossier'] = BASE_URL + str(node.findChildren('a', text='dossier')[0].parent.extract().attrs[0][1])
        except: pass
        try:
            links['analyse'] = BASE_URL + str(node.findChildren('a', text=re.compile(".*analyse.*"))[0].parent.extract().attrs[0][1])
        except: pass
        for occurence in node.findAll('br'):
            occurence.extract()
#        objet = node.contents[0].encode('utf-8').replace('\n','').rstrip('[')
        objet = node.renderContents().replace('\n','').replace('[]', '')
        return (objet, links)

    def parse_scrutin(self, raw):
        scrutin = {}
        # Get scrutin's id
        l = raw.find('td', {'class':'denom'})
        if l.find('a'):
            id = str(l.find('a').contents[0])
            scrutin['65-1'] = False
            if '*' in id:
                scrutin['id'] = int(id.rstrip('*'))
                scrutin['65-1'] = True
            else:
                scrutin['id'] = int(id)
        else:
            try:
                scrutin['id'] = int(str(l.contents[0]).lstrip('\n'))
            except:
                self.type_scrutin += 1
                return None
        # scrutin's type
        if self.type_scrutin == 1:
            scrutin['type'] = 'salles voisines'
        elif self.type_scrutin == 2:
            scrutin['type'] = 'ordinaire'
        # get session's date
        l = l.findNextSibling('td')
        scrutin['date'] = l.contents[0]

        # get vote's topic
        l = l.findNextSibling('td')
        (scrutin['objet'],
         scrutin['liens']) = self.parse_scrutin_objet(l)

        scrutin['vote'] = {}
        # get number of 'positive' votes
        l = l.findNextSibling('td')
        if l.contents[0] == '-':
            scrutin['vote']['pour'] = -1
        else:
            scrutin['vote']['pour'] = int(l.contents[0])
        # get number of 'negative' votes
        l = l.findNextSibling('td')
        if l.contents[0] == '-':
            scrutin['vote']['contre'] = -1
        else:
            scrutin['vote']['contre'] = int(l.contents[0])
        # get number of 'neutral' votes
        l = l.findNextSibling('td')
        if l.contents[0] == '-':
            scrutin['vote']['abstention'] = -1
        else:
            scrutin['vote']['abstention'] = int(l.contents[0])

        return scrutin

    def parse_document(self):
        scrutins = {}
        for raw_scrutin in self.document.findAll("td", {"class":"denom"}):
            scrutin = self.parse_scrutin(raw_scrutin.parent)
            if scrutin != None:
                scrutins[scrutin['id']] = scrutin
        return scrutins

class type_scrutin(dict):
    def dump(self, key):
        string = "  scrutin_"+str(key)+"\n"
        for kk, vv in self.iteritems():
            if type(vv) is dict:
                string += (2+4)*' '+str(kk)+": \n"
                for kkk, vvv in vv.iteritems():
                    if type(vvv) in (int, bool):
                        string += (2+6)*' '+str(kkk)+": "+str(vvv)+"\n"
                    else:
                        string += (2+6)*' '+'- '+str(kkk)+": \""+str(vvv)+"\"\n"
            else:
                if type(vv) in (int, bool):
                    string += (2+4)*' '+str(kk)+": "+str(vv)+"\n"
                else:
                    string += (2+4)*' '+str(kk)+": \""+str(vv)+"\"\n"
        return string

try:
    os.mkdir('scrutin')
except:
    pass

print "Vote grabber v0.2"
print "Processed vote number : ",
for k, s in ScrutinParser(url).parse_document().iteritems():
    with open("scrutin/"+str(k)+".yml", "w") as f:
        print k, " ",
        f.write(type_scrutin(s).dump(k))
print
