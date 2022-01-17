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

def hasPrefixIntervenant(s):
    for prefix in ['M.', 'Mme', 'Monsieur', 'Madame', 'Me ', 'Dr ', 'Grand rabbin', 'Adjudant', 'Capitaine', 'Caporal', 'Colonel', 'Commandant', 'Commissaire', 'Infirmier', 'Général', 'Major', 'Maitre', 'Maître', 'Premier', 'Sergent']:
        if s.find(prefix) == 0:
            return True
    return False


def cleanhtml(s):
    reg_center = re.compile('<p [^>]*text-align:center[^>]*>(.*)</p>')
    s = reg_center.sub('<p><i>\\1</i></p>', s)
    reg_normal = re.compile('<span [^>]*font-weight:normal[^>]*>(.*)</span>')
    s = reg_normal.sub('\\1', s)
    reg_boldanditalic = re.compile('<span [^>]*font-weight:bold; font-style:italic[^>]*>([^<]*)</span>')
    s = reg_boldanditalic.sub('<b><i>\\1</i></b>', s)
    reg_bold = re.compile('<span [^>]*font-weight:bold[^>]*>([^<]*)</span>')
    s = reg_bold.sub('<b>\\1</b>', s)
    reg_bold = re.compile('<span [^>]*class=.assnatStrong.[^>]*>([^<]*)</span>')
    s = reg_bold.sub('<b>\\1</b>', s)
    reg_bold = re.compile('<span [^>]*class=.assnatGras.[^>]*>([^<]*)</span>')
    s = reg_bold.sub('<b>\\1</b>', s)
    reg_italic = re.compile('<span [^>]*font-style:italic[^>]*>([^<]*)</span>')
    s = reg_italic.sub('<i>\\1</i>', s)
    reg_underline = re.compile('<span [^>]*text-decoration:underline[^>]*>([^<]*)</span>')
    s = reg_underline.sub('<i>\\1</i>', s)
    reg_span = re.compile('<span [^>]*>([^<]*)</span>')
    s = reg_span.sub('\\1', s)

    reg_parenthese = re.compile('</b>\)\.')
    s = reg_parenthese.sub(').</b>', s)

    reg_doubletag = re.compile('(</i><i>|</b><b>)')
    s = reg_doubletag.sub('', s)
    s = reg_doubletag.sub('', s)

    reg_doubletag = re.compile('(</i> +<i>|</b> +<b>|<i> +</i>|<b> +</b>)')
    s = reg_doubletag.sub(' ', s)
    s = reg_doubletag.sub(' ', s)
    reg_doubletag = re.compile('(</i>, +<i>|</b>, +<b>|<i>, +</i>|<b>, +</b>)')
    s = reg_doubletag.sub(', ', s)
    reg_doubletag = re.compile('(</i>[\'’]<i>|</b>[\'’]<b>|<i>[\'’]</i>|<b>[\'’]</b>)')
    s = reg_doubletag.sub('\'', s)

    reg_doubletag = re.compile('</i>(<[^>]*>)<i>')
    s = reg_doubletag.sub('\\1', s)
    reg_doubletag = re.compile('</b>(<[^>]*>)<b>')
    s = reg_doubletag.sub('\\1', s)

    reg_p = re.compile('<p([^>]*)style="[^>]*"([^>]*)> *')
    s = reg_p.sub('<p\\1 \\2>', s)

    s = s.replace('&#xa0;', ' ')

    s = s.replace('&#039;', "'")
    s = s.replace('’', "'")

    reg_spaces = re.compile(' (</(b|i)>)')
    s = reg_spaces.sub('\\1 ', s)
    reg_spaces = re.compile('(<(b|i)>) ')
    s = reg_spaces.sub(' \\1', s)

    reg_sautlignes = re.compile('<p([^>]*)>\n\s*')
    s = reg_sautlignes.sub('<p\\1>', s)
    reg_sautlignes = re.compile('\s*\n\s*</p>')
    s = reg_sautlignes.sub('</p>', s)

    s = s.replace(' , ', ', ')

    reg_br = re.compile('<br */?>')
    s = reg_br.sub(' ', s)

    reg_spaces = re.compile('  +')
    s = reg_spaces.sub(' ', s)
    
    reg_doubletag = re.compile('(</i> *<i>|</b> *<b>|<i> *</i>|<b> *</b>)')
    s = reg_doubletag.sub(' ', s)

    s = s.replace('<p >', '<p>')
    
    return s

def html2json(s):
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    soup = BeautifulSoup(s, features="lxml")
    p_tags = soup.find(class_="assnatSection1").find_all('p')
    source = source_url

    # Meta
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
            heures = re.findall(r'(\d+) *h(?:eures?)? *(\d*)', p_text.lower())
            if len(heures) > 0 and heures[0][0]:
                heure = "%02d:" % int(heures[0][0])
                if (len(heures[0]) > 1 and heures[0][1]):
                    heure += "%02d" % int(heures[0][1])
                else:
                    heure += '00'
            continue
        if (p_text.find('session ') == 0):
            i = p_text.find(' 20')
            session = p_text[i+1:].replace('-', '')

    # Interventions
    try:
        p_tags = soup.find(class_="assnatSection2").find_all(['p', 'h1', 'h2', 'h3', 'h3', 'table'], recursive=False)
    except AttributeError:
        print("ERROR: "+ sys.argv[1]+" n'a pas de section assnatSection2 permettant d'identifier le corps du compte-rendu. Merci de l'ajouter à la main")
        exit(2)

    intervention = ''
    for p in p_tags:
        if p.get_attribute_list('class') and p.get_attribute_list('class')[0] and p.get_attribute_list('class')[0].find('Titre') > 0:
            new_intervention()
            intervention = '<p><h3>' + p.get_text() + '</h3></p>'
            new_intervention()
            continue
        a_tags = p.find_all('a')
        for a in a_tags:
            if a.get('id'):
                source = source_url+"#"+a.get('id')
                a.unwrap()
                break
        b = p.find('b')
        if (b):
            if (hasPrefixIntervenant(b.get_text()) or (not b.get_text().find('amendement') and b.get_text().find(' (') and b.get_text()[-2:] == ').')):
                new_intervention()
                intervenant = b.get_text()
                b.clear()
                b.unwrap()
            else:
                b_str = str(b)
                if (b_str.find('</b>') > 0) and b_str[b_str.find('</b>'):] == '</b>' and len(b_str) > 8 and b_str.find('<b>') < 100:
                    new_intervention()
                    if (b_str.find(')</b>') > 0 or b_str.find(').</b>') > 0) and not str(p).find('</b></p>') > 0 and not re.search(r'\d', str(p)):
                        intervenant = b.get_text()
                        b.clear()
                        b.unwrap()
        span_tags = p.find_all('span')
        for span in span_tags:
            span.unwrap()
        if p.name.find('h') == 0:
            new_intervention()
            intervention = '<p><h3>' + p.get_text() + '</h3></p>'
            new_intervention()
            continue;
        if p.name.find('table') == 0:
            new_intervention()
            intervention = str(p)
            new_intervention()
            continue;
        p_str = str(p)
        p_str = re.sub(r'<p[^>]*>', '<p>', p_str)
        p_str = p_str.replace('\xa0', ' ')
        if p_str.find("<p></p>") == 0:
            continue
        if (p_str.find('<p>–') == 0 or p_str.find('<p>----') == 0 or p_str.find('<p>__') == 0 or p_str.find('<p><i>—') == 0 or p_str.find('<p><i>__') == 0 or p_str.find('<p><b>–') == 0 or p_str.find('<p>—') == 0 or p_str.find('<p><b>—') == 0) and (p_str.find('–<') > 0 or p_str.find('----<') > 0 or p_str.find('_<') > 0 or p_str.find('—<') > 0 or p_str.find('—<') > 0):
            continue
        if p_str.find('<p>*</p>') == 0 :
            if (intervenant):
                new_intervention()
        if p_str.find('<i>(') > 0 and p_str.find(')</i>') > 0 :
            didascalie = re.findall(r'(.*)(<i>\([^)]*\)</i>)( *.? *</p>)', p_str)
            if(didascalie):
                intervention += didascalie[0][0] + didascalie[0][2]
                oldintervenant = intervenant
                new_intervention()
                intervention = '<p>'+didascalie[0][1]+'</p>'
                new_intervention()
                intervenant = oldintervenant
                continue
        elif p_str.find('<p><i>') == 0 and (p_str.find('></p>') > 0 or p_str.find('>.</p>') > 0):
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
            source_backup = source
            intervention_video(p)
            source = source_backup
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
            urlvideotimestamp = "https://videos.assemblee-nationale.fr/Datas/an/%s/content/finalplayer.nvs" % videoid
            response = requests_get(urlvideo_meta)
            soupvideo = BeautifulSoup(response['content'], features="lxml")
            response = requests_get(urlvideotimestamp)
            souptimestamp = BeautifulSoup(response['content'], features="lxml")
        except IndexError:
            soupvideo = None
            souptimestamp = None
    if not souptimestamp or not soupvideo:
        return
    videotimestamps = {}
    for videotimestamp_tag in souptimestamp.find_all('synchro'):
        videotimestamps[videotimestamp_tag.get('id')] = videotimestamp_tag.get('timecode')
    for chapter in soupvideo.find_all('chapter'):
        videotimestamp = 0
        videotimestamp_thumbnail = 0
        ahtmltimestamp = ''
        imagehtmlthumbnail = ''
        if videotimestamps.get(chapter.get('id')):
            videotimestamp = int(videotimestamps[chapter.get('id')])
            ahtmltimestamp = "<a href='"+urlvideo+"?timecode="+str(videotimestamp)+"'>"
            source = urlvideo+"?timecode="+str(videotimestamp)
        if videotimestamp:
            videotimestamp_thumbnail = int((videotimestamp / 1000) / 60 + 1) * 60
        if videotimestamp_thumbnail:
            urlthumbnail = "http://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, videotimestamp_thumbnail)
            imagethumbnail = requests_get(urlthumbnail)
            if (imagethumbnail['content_type'] == 'error'):
                urlthumbnail = "http://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, videotimestamp_thumbnail + 1)
                imagethumbnail = requests_get(urlthumbnail)
            if (imagethumbnail and imagethumbnail['content_type'] != 'error'):
                imagehtmlthumbnail = "<img src='data:%s;base64,%s'/>" % (imagethumbnail['content_type'], imagethumbnail['content'])
        new_intervention()
        if hasPrefixIntervenant(chapter.get('label')):
            intervenant = chapter.get('label')
            intervention = ''
        else:
            intervention = "<p><h4>"+chapter.get('label')+"</h4></p>"
            continue
        intervention += "<p>Intervention uniquement disponible en vidéo.<br/><br/><br/></p>"
        intervention += "<center><p>"
        if imagehtmlthumbnail:
            if ahtmltimestamp:
                intervention += ahtmltimestamp
            intervention += imagehtmlthumbnail
            if ahtmltimestamp:
                intervention += "</a>"
                intervention += "</p></center><center><p>"
        if ahtmltimestamp:
            intervention += ahtmltimestamp + "<i>Consulter la vidéo en cliquant sur la miniature</i></a>"
        intervention += "</p></center>"
        new_intervention()

def new_intervention():
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    #{"commission": "commission des finances, de l'économie générale et du contrôle budgétaire", "intervention": "<p>Présidence de M. Éric Woerth, Président</p>", "date": "2020-01-15", "source": "http://www.assemblee-nationale.fr/15/cr-cfiab/19-20/c1920037.asp#P9_450", "heure": "09:30", "session": "20192020", "intervenant": "", "timestamp": "37000020"}
    intervenant = intervenant.replace('\xa0', ' ')
    intervention = intervention.replace('\xa0', ' ')
    intervention = intervention.replace('\xa0', ' ')
    intervention = intervention.replace('\n', ' ')
    intervention = intervention.replace('<i> </i>', ' ')
    intervention = intervention.replace('<b> </b>', ' ')
    intervention = intervention.replace('</i> <i>', ' ')
    intervention = intervention.replace('</b> <b>', ' ')
    intervention = intervention.replace('</i><i>', '')
    intervention = intervention.replace('</b><b>', '')
    intervention = intervention.replace('<h3></h3>', '')
    intervention = intervention.replace('</i><b><i>', '<b>')
    intervention = intervention.replace('</i></b><i>', '</b>')
    intervention = intervention.replace('<i></i>', ' ')
    intervention = intervention.replace('<b></b>', ' ')
    intervention = intervention.replace('<i></i>', ' ')
    intervention = intervention.replace('<p></p>', '')
    intervention = intervention.replace('<p>*</p>', '')
    intervention = intervention.replace('<p>* *</p>', '')
    intervention = intervention.replace('<p> </p>', '')
    intervention = intervention.replace('<p>. ', '<p>')

    intervention = re.sub(r'<a id="[^""]*">([^<]*)</a>', r'\1 ', intervention)
    intervention = re.sub(r'([^> ])<b>', r'\1 <b>', intervention)
    intervention = re.sub(r'</b>([^< \.])', r'</b> \1', intervention)
    intervention = re.sub(r'([^> ])<i>', r'\1 <i>', intervention)
    intervention = re.sub(r'</i>([^< \.])', r'</i> \1', intervention)
    intervention = re.sub(r' style="[^"]+"', r' ', intervention)
    intervention = re.sub(r'([a-z])É([a-z])', r'\1é\2', intervention)
    intervention = re.sub(r'([a-z])É([a-z])', r'\1é\2', intervention)
    intervention = re.sub(r'([a-z])È([a-z])', r'\1è\2', intervention)
    intervention = re.sub(r'([a-z])È([a-z])', r'\1è\2', intervention)
    intervention = re.sub(r'([a-z])Ê([a-z])', r'\1ê\2', intervention)
    intervention = re.sub(r'([a-z])Ê([a-z])', r'\1ê\2', intervention)
    intervention = re.sub(r'([a-z]) À ([a-z])', r'\1 à \2', intervention)
    intervention = re.sub(r'<p[^>]*>', '<p>', intervention)
    intervention = re.sub(r'<p> *', '<p>', intervention)

    [intervenant, fonction] = getIntervenantFonction(intervenant)
    timestamp += 10
    curtimestamp = timestamp
    if (intervention):
        intervenants = intervenant.split(' et ')
        if len(intervenants) > 1:
            intervenant = "M "+intervenants[0]
            linterventioncommune = intervention
            new_intervention()
            curtimestamp += 1
            intervenant = intervenants[1]
            intervention = linterventioncommune
            [intervenant, fonction] = getIntervenantFonction(intervenant)
        print(json.dumps({"commission": commission, "intervention": intervention, "date": date, "source": source, "heure": heure, "session": session, "intervenant": intervenant, "timestamp": curtimestamp, "fonction": fonction }, ensure_ascii=False))
    intervenant = ''
    intervention = ''

def getIntervenantFonction(intervenant):
    global intervenant2fonction, fonction2intervenant
    fonction = ''
    intervenant_sexe = ''
    if intervenant.find('M.') == 0 or intervenant.find('M ') == 0 or intervenant.find('Mme') == 0 or intervenant.find('Monsieur') == 0 or intervenant.find('Madame') == 0 :
        if intervenant.find('M.') == 0 or intervenant.find('M ') == 0 or intervenant.find('Monsieur') == 0:
            intervenant_sexe = '|M|'
        if intervenant.find('Mme') == 0 or intervenant.find('Madame') == 0 :
            intervenant_sexe = '|F|'
        intervenant = ' '.join(intervenant.split(' ')[1:])
    intervenant = re.sub(r'\. *$', '', intervenant)
    intervenant = re.sub(r' *$', '', intervenant)
    if intervenant.find('le ') == 0 or intervenant.find('Le ') == 0 :
        intervenant_sexe = '|M|'
    if intervenant.find('la ') == 0 or intervenant.find('La ') == 0 :
        intervenant_sexe = '|F|'
    if fonction2intervenant.get(intervenant_sexe+re.sub(r'^la?e? ', '', intervenant)):
        fonction = intervenant
        intervenant = fonction2intervenant[intervenant_sexe+re.sub(r'^la?e? ', '', intervenant)]
    elif fonction2intervenant.get(re.sub(r'^la?e? ', '', intervenant)):
        fonction = intervenant
        intervenant = fonction2intervenant[re.sub(r'^la?e? ', '', intervenant)]
    intervenantfonction = re.findall(r'([^,;]*|présidente?)[,;] ([^\.]*)', intervenant, re.IGNORECASE)
    if (len(intervenantfonction) > 0 and not intervenantfonction[0][0].lower().find('président') >= 0):
        [intervenant, fonction] = intervenantfonction[0]
    prez = re.findall(r'([^,<]*président?c?e?|c?o?-?rapporteure?)[,;]? (..[^\.,;]*)([,;] [^\.]*)?', intervenant, re.IGNORECASE)
    if prez and prez[0][1].find('général') != 0:
        [fonction2, intervenant, fonction3] = prez[0]
        if (fonction):
            fonction = fonction2 + ', ' + fonction + fonction3
        else:
            fonction = fonction2 + fonction3
    if (not fonction and intervenant2fonction.get(intervenant)):
        fonction = intervenant2fonction[intervenant]
    elif(fonction):
        intervenant2fonction[intervenant] = fonction
        fonction = re.sub(r'^la?e? ', '', fonction)
        fonction2intervenant[fonction] = intervenant
        fonction2intervenant[intervenant_sexe+fonction] = intervenant
        if fonction.find('rapporteure générale') >= 0:
            fonction2intervenant['rapporteure générale'] = intervenant
            fonction2intervenant['|F|rapporteure générale'] = intervenant
        elif fonction.find('rapporteur général') >= 0:
            fonction2intervenant['rapporteur général'] = intervenant
            fonction2intervenant[intervenant_sexe+'rapporteur général'] = intervenant
        elif fonction.find('rapporteure spéciale') >= 0:
            fonction2intervenant['rapporteure spéciale'] = intervenant
            fonction2intervenant['|F|rapporteure spéciale'] = intervenant
        elif fonction.find('rapporteur spécial') >= 0:
            fonction2intervenant['rapporteur spécial'] = intervenant
            fonction2intervenant[intervenant_sexe+'rapporteur spécial'] = intervenant
        elif fonction.find('rapporteure') >= 0:
            fonction2intervenant['rapporteure'] = intervenant
            fonction2intervenant['|F|rapporteure'] = intervenant
        elif fonction.find('rapporteur') >= 0:
            fonction2intervenant['rapporteur'] = intervenant
            fonction2intervenant[intervenant_sexe+'rapporteur'] = intervenant
        if fonction.find('ministre') >= 0:
            fonction2intervenant['ministre'] = intervenant
            fonction2intervenant[intervenant_sexe+'ministre'] = intervenant
        if fonction.find("secrétaire d'État") >= 0:
            fonction2intervenant["secrétaire d'État"] = intervenant
            fonction2intervenant[intervenant_sexe+"secrétaire d'État"] = intervenant
        fonctionaralonge = re.findall('([^,]*), ([^,]*)', fonction)
        if fonctionaralonge: 
            fonction2intervenant[fonctionaralonge[0][0]] = intervenant
            fonction2intervenant[intervenant_sexe+fonctionaralonge[0][0]] = intervenant
    return [intervenant, fonction]

def requests_get(url):
    global content_file
    cache_file = "%s_%s.cache" % (content_file, urllib.parse.quote(url, '.'))
    response = None
    if "--use-cache" in sys.argv:
        try:
            with open(cache_file, 'r', encoding='utf-8') as cachefile:
                response = json.load(cachefile)
                return response
        except:
            pass
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
    with open(cache_file,'w+', encoding='utf-8') as out_file:
        json.dump(response, out_file)
    return response

source_url = sys.argv[2]
content_file = sys.argv[1]
with open(content_file, encoding='utf-8') as f:
    raw_html = f.read()
    html = cleanhtml(raw_html)
    html2json(html)
