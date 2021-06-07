#!/usr/bin/env python
# -*- coding: utf8 -*-
import sys
import re
from bs4 import BeautifulSoup
import requests
import base64
import json
import urllib

mois2nmois = {'janvier': 1, 'février': 2, 'mars': 3, 'avril': 4, 'mai': 5, 'juin':6, 'juillet': 7, 'août': 8, 'septembre': 9, 'octobre': 10, 'novembre': 11, 'décembre': 12}

global commission, date, heure, session, source, intervenant, intervention, timestamp, fonction, intervenant2fonction,fonction2intervenant, content_file

[commission, date, heure, session, source, intervenant, intervention, fonction] = ['', '', '', '', '', '', '', '']
intervenant2fonction = {}
fonction2intervenant = {}
timestamp = 0

def cleanhtml(s):
    reg_bold = re.compile('<span [^>]*font-weight:bold[^>]*>([^<]*)</span>')
    s = reg_bold.sub('<b>\\1</b>', s)
    reg_italic = re.compile('<span [^>]*font-style:italic[^>]*>([^<]*)</span>')
    s = reg_italic.sub('<i>\\1</i>', s)
    reg_span = re.compile('<span [^>]*>([^<]*)</span>')
    s = reg_span.sub('\\1', s)
    reg_doubletag = re.compile('(</i><i>|</b><b>)')
    s = reg_doubletag.sub('', s)
    reg_doubletag = re.compile('(</i> +<i>|</b> +<b>)')
    s = reg_doubletag.sub(' ', s)
    reg_boldinitalic = re.compile('</i><b>([^<]*)</b><i>')
    s = reg_boldinitalic.sub('<b>\\1</b>', s)
    
    reg_p = re.compile('<p [^>]*>')
    s = reg_p.sub('<p>', s)
    
    s = s.replace('&#xa0;', '&nbsp;')
    
    reg_spaces = re.compile(' (</(b|i)>)')
    s = reg_spaces.sub('\\1 ', s)
    reg_spaces = re.compile('(<(b|i)>) ')
    s = reg_spaces.sub(' \\1', s)

    reg_sautlignes = re.compile('<p>\n\s*')
    s = reg_sautlignes.sub('<p>', s)
    reg_sautlignes = re.compile('\s*\n\s*</p>')
    s = reg_sautlignes.sub('</p>', s)

    reg_br = re.compile('<br */>')
    s = reg_br.sub(' ', s)

    reg_spaces = re.compile('  +')
    s = reg_spaces.sub(' ', s)

    return s

def html2json(s):
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    soup = BeautifulSoup(s, features="lxml")
    p_tags = soup.find(class_="assnatSection1").find_all('p')
    source = source_url
    cpt = 0

    #Meta
    for p in p_tags:
        p_text = p.get_text()
        p_text = p_text.replace('\xa0', ' ')
        if (p_text.find('Commission') == 0 or p_text.find('Délégation') == 0 or p_text.find('Mission') == 0 or p_text.find('Office') == 0 or p_text.find('Comité') == 0):
            commission = p_text
        for wday in ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'] + list(mois2nmois.keys()):
            if (p_text.lower().find(wday) >= 0):
                try:
                    days = re.findall(r'(\d+)e?r? *([^ \d]+) +(\d+)', p_text)
                    if len(days) > 0:
                        date = "%04d-%02d-%02d" % (int(days[0][2]), mois2nmois[days[0][1].lower()], int(days[0][0]))
                        if (mois2nmois[days[0][1].lower()] > 8):
                            session = days[0][2]+str(int(days[0][2]) + 1)
                        else:
                            session = str(int(days[0][2]) - 1)+days[0][2]
                        break
                except KeyError:
                    continue
        if (p_text.lower().find(' heure') > -1 or p_text.find(' h ') > -1):
            heures = re.findall(r'(\d+) *(h|heures?) *(\d*)', p_text.lower())
            if len(heures) > 0 and heures[0][0]: 
                heure = "%02d:" % int(heures[0][0])
                if (len(heures[0]) > 2 and heures[0][2]):
                    heure += "%02d" % int(heures[0][2])
                else:
                    heure += '00'
            continue
        if (p_text.find('session ') == 0): 
            i = p_text.find(' 20')
            session = p_text[i+1:].replace('-', '')
        if (p_text.find('Compte rendu n° ') == 0):
            cpt = int(p_text[16:]) * 1000000
    
    #Intervensions
    try:
        p_tags = soup.find(class_="assnatSection2").find_all(['p', 'h1', 'h2', 'h3', 'h3'])
    except AttributeError:
        print("ERROR: "+ sys.argv[1]+" n'a pas de section assnatSection2 permettant d'identifier le corps du compte-rendu. Merci de l'ajouter à la main")
        exit(2)

    intervention = ''
    for p in p_tags:
        cpt += 10
        timestamp = cpt
        b = p.find('b')
        if (b):
            if (b.get_text().find('M.') == 0 or b.get_text().find('Mme') == 0 or b.get_text().find('Monsieur') == 0 or b.get_text().find('Madame') == 0):
                new_intervention()
                intervenant = b.get_text()
                b.clear()
                b.unwrap()
        a_tags = p.find_all('a')
        for a in a_tags:
            if a.get('id'):
                source = source_url+"#"+a.get('id')
                a.unwrap()
        span_tags = p.find_all('span')
        for span in span_tags:
            span.unwrap()
        if p.name.find('h') == 0:
            new_intervention()
            intervention = '<p><h3>' + p.get_text() + '</h3></p>'
            new_intervention()
            continue;
        p_str = str(p)
        p_str = p_str.replace('\xa0', ' ')
        if p_str.find('<p>—  1') == 0:
            continue
        if p_str.find('<i>(') > 0 and p_str.find(')</i>') > 0 :
            didascalie = re.findall(r'(.*)(<i>\([^)]*\)</i>)(</p>)', p_str)
            if(didascalie):
                intervention += didascalie[0][0] + didascalie[0][2]
                new_intervention()
                intervention = '<p>'+didascalie[0][1]+'</p>'
                continue
        elif p_str.find('<p><i>') == 0 and p_str.find('</i></p>') > 0:
            if (intervenant):
                new_intervention()
            p_str = p_str.replace('<i>', '')
            p_str = p_str.replace('</i>', '')
            intervention += p_str
            continue
        br = p.find('br')
        if (br):
            br.unwrap()
        p = str(p)
        if p.find('videos.assemblee-nationale.fr') >= 0 or p.find('assnat.fr') >= 0 :
            intervention += p
            new_intervention()
            intervention_video(p)
            continue
        intervention += p
    new_intervention()

def intervention_video(p):
    global commission, date, heure, session, source, intervenant, intervention, timestamp, content_file
    soupvideo = None
    souptimestamp = None
    if not soupvideo or not souptimestamp: 
        video = re.findall(r'(https?://videos.assemblee-nationale.fr/video\.([^\.]*)\.[^<"\']+)', p)
        if not len(video):
            url = re.findall(r'(https?://[^<"\']+)', p)
            response = requests_get(url[0])
            video = re.findall(r'(https?://videos.assemblee-nationale.fr/video\.([^\.]*)(\.[^<"\']+|))', response['url'])
        try:
            videoid = video[0][1]
            urlvideo = video[0][0]
            urlvideo_meta = "http://videos.assemblee-nationale.fr/Datas/an/%s/content/data.nvs" % videoid
            urltimestamp = "https://videos.assemblee-nationale.fr/Datas/an/%s/content/finalplayer.nvs" % videoid
            response = requests_get(urlvideo_meta)
            soupvideo = BeautifulSoup(response['content'], features="lxml")
            response = requests_get(urltimestamp)
            souptimestamp = BeautifulSoup(response['content'], features="lxml")
        except IndexError:
            soupvideo = None
            souptimestamp = None
    if not souptimestamp or not soupvideo:
        return
    timestamps = {}
    for timestamp_tag in souptimestamp.find_all('synchro'):
        timestamps[timestamp_tag.get('id')] = timestamp_tag.get('timecode')
    for chapter in soupvideo.find_all('chapter'):
        timestamp = 0
        timestamp_thumbnail = 0
        ahtmltimestamp = ''
        imagehtmlthumbnail = ''
        if timestamps.get(chapter.get('id')):
            timestamp = int(timestamps[chapter.get('id')])
            ahtmltimestamp = "<a href='"+urlvideo+"?timecode="+str(timestamp)+"'>"
            source = urlvideo+"?timecode="+str(timestamp)
        if timestamp:
            timestamp_thumbnail = int((timestamp / 1000) / 60 + 1) * 60
        if timestamp_thumbnail:
            urlthumbnail = "http://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, timestamp_thumbnail)
            imagethumbnail = requests_get(urlthumbnail)
            if (imagethumbnail['content_type'] == 'error'):
                urlthumbnail = "http://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, timestamp_thumbnail + 1)
                imagethumbnail = requests_get(urlthumbnail)
            if (imagethumbnail and imagethumbnail['content_type'] != 'error'):
                imagehtmlthumbnail = "<img src='data:%s;base64,%s'/>" % (imagethumbnail['content_type'], imagethumbnail['content'])
        new_intervention()
        if (chapter.get('label').find('M.') == 0 or chapter.get('label').find('Mme') == 0):
            intervenant = chapter.get('label')
            intervention = ''
        else:
            intervention = "<p><h4>"+chapter.get('label')+"</h4></p>"
            continue
        intervention += "<p>"
        if imagehtmlthumbnail: 
            if ahtmltimestamp:
                intervention += ahtmltimestamp
            intervention += imagehtmlthumbnail
            if ahtmltimestamp:
                intervention += "</a>"
                intervention += "</p><p>"
        if ahtmltimestamp:
            intervention += ahtmltimestamp
        intervention += "<i>(disponible uniquement en vidéo)</i>"
        if ahtmltimestamp:
            intervention += "</a>"
        intervention += "</p>"
    
def new_intervention():
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    #{"commission": "commission des finances, de l'économie générale et du contrôle budgétaire", "intervention": "<p>Présidence de M. Éric Woerth, Président</p>", "date": "2020-01-15", "source": "http://www.assemblee-nationale.fr/15/cr-cfiab/19-20/c1920037.asp#P9_450", "heure": "09:30", "session": "20192020", "intervenant": "", "timestamp": "37000020"}
    intervenant = intervenant.replace('\xa0', ' ')
    intervention = intervention.replace('\xa0', ' ')
    intervention = intervention.replace('\xa0', ' ')
    intervention = intervention.replace('\n', ' ')
    intervention = intervention.replace('> ', '>')
    intervention = intervention.replace(' </', '</')
    intervention = intervention.replace('<i> </i>', ' ')
    intervention = intervention.replace('<b> </b>', ' ')
    intervention = intervention.replace('</i><i>', '')
    intervention = intervention.replace('</b><b>', '')
    intervention = intervention.replace('</i> <i>', '')
    intervention = intervention.replace('</b> <b>', '')
    intervention = intervention.replace('<h3></h3>', '')
    intervention = intervention.replace('<p></p>', '')
    intervention = intervention.replace('<p> </p>', '')
    [intervenant, fonction] = getIntervenantFonction(intervenant)
    if (intervention):
        intervenants = intervenant.split(' et ')
        if len(intervenants) > 1:
            intervenant = "M "+intervenants[0]
            linterventioncommune = intervention
            new_intervention()
            timestamp += 1
            intervenant = intervenants[1]
            intervention = linterventioncommune
            [intervenant, fonction] = getIntervenantFonction(intervenant)
        print(json.dumps({"commission": commission, "intervention": intervention, "date": date, "source": source, "heure": heure, "session": session, "intervenant": intervenant, "timestamp": timestamp, "fonction": fonction }))
    intervenant = ''
    intervention = ''

def getIntervenantFonction(intervenant):
    global intervenant2fonction, fonction2intervenant
    fonction = ''
    intervenant = ' '.join(intervenant.split(' ')[1:])
    intervenant = re.sub(r'\. *$', '', intervenant)
    if fonction2intervenant.get(re.sub(r'^la?e? ', '', intervenant)):
        fonction = intervenant
        intervenant = fonction2intervenant[re.sub(r'^la?e? ', '', intervenant)]
    intervenantfonction = re.findall(r'([^,;]*|présidente?)[,;] ([^\.]*)', intervenant, re.IGNORECASE)
    if (len(intervenantfonction) > 0 and not intervenantfonction[0][0].lower().find('président') >= 0):
        [intervenant, fonction] = intervenantfonction[0]
    prez = re.findall(r'([^,<]*président?c?e?|c?o?-?rapporteure?)[,;]? ([^\.,;]*)([,;] [^\.]*)?', intervenant, re.IGNORECASE)
    if prez:
        [fonction2, intervenant, fonction3] = prez[0]
        if (fonction):
            fonction = fonction2 + ', ' + fonction + fonction3
        else:
            fonction = fonction2 + fonction3
    if (not fonction and intervenant2fonction.get(intervenant)):
        fonction = intervenant2fonction[intervenant]
    elif(fonction):
        intervenant2fonction[intervenant] = fonction
        fonction2intervenant[fonction] = intervenant
    return [intervenant, fonction]

def requests_get(url):
    global content_file
    cache_file = "%s_%s.cache" % (content_file, urllib.parse.quote(url, '.'))
    try:
        with open(cache_file, 'r') as cachefile:
            response = json.load(cachefile)
            return response
    except:
        response = None
    request = requests.get(url)
    try:
        contenttype = request.headers['Content-Type']
    except KeyError:
        contenttype = 'error'
    if contenttype.find('text') == 0:
        content = request.text
    else:
        content = base64.b64encode(request.content).decode("utf-8")
    response = {'content': content, 'url': request.url, 'content_type': contenttype}
    with open(cache_file,'w+') as out_file:
        json.dump(response, out_file)
    return response

source_url = sys.argv[2]
content_file = sys.argv[1]
with open(content_file) as f:
    raw_html = f.read()
    html = cleanhtml(raw_html)
    html2json(html)
