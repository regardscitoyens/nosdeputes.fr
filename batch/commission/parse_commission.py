#!/usr/bin/env python3
# -*- coding: utf8 -*-
import sys
import re
from bs4 import BeautifulSoup, XMLParsedAsHTMLWarning
import requests
import base64
import json
import urllib
import warnings
warnings.filterwarnings('ignore', category=XMLParsedAsHTMLWarning)

mois2nmois = {'janvier': 1, 'février': 2, 'mars': 3, 'avril': 4, 'mai': 5, 'juin':6, 'juillet': 7, 'août': 8, 'septembre': 9, 'octobre': 10, 'novembre': 11, 'décembre': 12}

prefixes_noms = [
    'M ', 'M.', 'Madame', 'MM.', 'Mme', 'Monsieur', 'Un député', 'Une députée',
    'Adjudant',  'Amiral', 'Capitaine', 'Caporal', 'CGA ', 'Colonel', 'Commandant', 'Commissaire', 'Cr1 ', 'Dr ', 'Dr. ', 'Général', 'Grand rabbin', 'Ica ', 'Infirmier', 'La policière', 'La présidente', 'Le Président', 'Lieutenant', 'Sergent', 'Son Exc. ', 'Maitre', 'Maître', 'Major', 'Me ', 'Médecin général', 'Plusieurs députés', 'Pr ', 'Pr. ', 'Premier', 'Professeur', 'Quatrier-Maitre', 'Une auditrice',
    'Agnès', 'Alain', 'Alexandre', 'André', 'Brigitte', 'Bruno', 'Cédric', 'Charles', 'Claude', 'Delphine', 'Didier', 'Elisa', 'Emmanuel', 'Fabien', 'Fabrice', 'Francis', 'Frédéric', 'Gilles', 'Gwendal', 'Jacques', 'Jean', 'Julien', 'Laurent', 'Loïc', 'Marc', 'Marie', 'Michèle', 'Nicole', 'Olivier', 'Patrice', 'Pierre', 'Raphaël', 'Rémi', 'Stéphane', 'Thibault', 'Thierry', 'Thomas', 'Ugo ', 'Vincent', 'Yann', 'Yannick'
]

global commission, date, heure, session, source, intervenant, intervention, timestamp, fonction, intervenant2fonction,fonction2intervenant, content_file

[commission, date, heure, session, source, intervenant, intervention, fonction] = ['', '', '', '', '', '', '', '']
intervenant2fonction = {}
fonction2intervenant = {}
timestamp = 0

def hasPrefixIntervenant(s):
    for prefix in prefixes_noms:
        if s.find(prefix) == 0:
            return True
    return False


def cleanhtml(s):
    s = re.sub(r'\t', ' ', s)

    # Assemble metas tags split into two lines such as the commission's name
    s = re.sub(r'(<p class="assnat[A-Z]+")([^>]*>)[\n\s\t]*(.*?)[\n\s\t]*</p>[\n\s\t]*\1[^>]*>[\n\s\t]*(.*?)[\n\s\t]*</p>', r'\1\2\3 \4</p>', s)

    reg_center = re.compile(r'<p [^>]*text-align:center[^>]*>(.*)</p>')
    s = reg_center.sub(r'<p><i>\1</i></p>', s)
    reg_bold = re.compile(r'(<p [^>]*)class=.assnatRubrique2.([^>]*>)\s*(.*?)\s*</p>')
    s = reg_bold.sub(r'\1 \2<b>\3</b></p>', s)
    s = re.sub(r'<p [^>]*class=.assnatNom[^>]*>\s*([^<]*)\s*<span [^>]*font-weight:normal[^>]*>(.*?)</span>', r'<p><b>\1</b>\2', s)
    reg_normal = re.compile(r'<span [^>]*font-weight:normal[^>]*>(.*)</span>')
    s = reg_normal.sub(r'\1', s)
    reg_boldanditalic = re.compile(r'<span [^>]*font-weight:bold; font-style:italic[^>]*>([^<]*)</span>')
    s = reg_boldanditalic.sub(r'<b><i>\1</i></b>', s)
    reg_bold = re.compile(r'<span [^>]*font-weight:bold[^>]*>([^<]*)</span>')
    s = reg_bold.sub(r'<b>\1</b>', s)
    reg_bold = re.compile(r'<span [^>]*class=.assnatStrong.[^>]*>([^<]*)</span>')
    s = reg_bold.sub(r'<b>\1</b>', s)
    reg_bold = re.compile(r'<span [^>]*class=.assnatGras.[^>]*>([^<]*)</span>')
    s = reg_bold.sub(r'<b>\1</b>', s)
    reg_italic = re.compile(r'<span [^>]*font-style:italic[^>]*>([^<]*)</span>')
    s = reg_italic.sub(r'<i>\1</i>', s)
    s = re.sub(r'(?:\(<i>|<i>\()([^<]*)(?:</i>\)|\)</i>)', r'<i>(\1)</i>', s)
    reg_underline = re.compile(r'<span [^>]*text-decoration:underline[^>]*>([^<]*)</span>')
    s = reg_underline.sub(r'<i>\1</i>', s)
    reg_span = re.compile(r'<span [^>]*>([^<]*)</span>')
    s = reg_span.sub(r'\1', s)

    reg_parenthese = re.compile(r'</b>\)\.')
    s = reg_parenthese.sub(r').</b>', s)

    reg_doubletag = re.compile(r'(</i><i>|</b><b>)')
    s = reg_doubletag.sub(r'', s)
    s = reg_doubletag.sub(r'', s)

    reg_doubletag = re.compile(r'(</i> +<i>|</b> +<b>|<i> +</i>|<b> +</b>)')
    s = reg_doubletag.sub(r' ', s)
    s = reg_doubletag.sub(r' ', s)
    reg_doubletag = re.compile(r'(</i>, +<i>|</b>, +<b>|<i>, +</i>|<b>, +</b>)')
    s = reg_doubletag.sub(r', ', s)
    reg_doubletag = re.compile(r'(</i>[\'’]<i>|</b>[\'’]<b>|<i>[\'’]</i>|<b>[\'’]</b>)')
    s = reg_doubletag.sub(r'\'', s)

    reg_doubletag = re.compile(r'</i>(<[^>]*>)<i>')
    s = reg_doubletag.sub(r'\1', s)
    reg_doubletag = re.compile(r'</b>(<[^>]*>)<b>')
    s = reg_doubletag.sub(r'\1', s)

    reg_p = re.compile(r'<p([^>]*)style="[^>]*"([^>]*)> *')
    s = reg_p.sub(r'<p\1 \2>', s)

    s = s.replace('&#xa0;', ' ')

    s = s.replace('&#039;', "'")
    s = s.replace("&#x2011;", "-")
    s = s.replace('’', "'")

    reg_spaces = re.compile(r' (</(b|i)>)')
    s = reg_spaces.sub(r'\1 ', s)
    reg_spaces = re.compile(r'(<(b|i)>) ')
    s = reg_spaces.sub(r' \1', s)

    reg_sautlignes = re.compile(r'<p([^>]*)>\n\s*')
    s = reg_sautlignes.sub(r'<p\1>', s)
    reg_sautlignes = re.compile(r'\s*\n\s*</p>')
    s = reg_sautlignes.sub(r'</p>', s)

    s = s.replace(' , ', ', ')

    reg_br = re.compile(r'<br */?>')
    s = reg_br.sub(r' ', s)

    reg_spaces = re.compile(r'  +')
    s = reg_spaces.sub(r' ', s)

    reg_doubletag = re.compile(r'(</i> *<i>|</b> *<b>|<i> *</i>|<b> *</b>)')
    s = reg_doubletag.sub(r' ', s)
    reg_doubletag = re.compile(r'\s*(</i>\s*-\s*<i>|</b>\s*-\s*<b>|<i>\s*-\s*</i>|<b>\s*-\s*</b>)\s*')
    s = reg_doubletag.sub(r' - ', s)

    s = s.replace('<p >', '<p>')

    s = re.sub(r'(<b>[^<]{5,}\.)( [A-ZÉÊÈÀÇ].?.?)(</b>)', r'\1\3\2', s)

    s = s.replace("heures heures", "heures")

    return s

heures_str = {
    "zéro": 0,
    "une": 1,
    "deux": 2,
    "trois": 3,
    "quatre": 4,
    "cinq": 5,
    "six": 6,
    "sept": 7,
    "huit": 8,
    "neuf": 9,
    "dix": 10,
    "onze": 11,
    "douze": 12,
    "treize": 13,
    "quatorze": 14,
    "quinze": 15,
    "seize": 16,
    "dix-sept": 17,
    "dix-huit": 18,
    "dix-neuf": 19,
    "vingt": 20,
    "vingt et une": 21,
    "vingt-et-une": 21,
    "vingt-deux": 22,
    "vingt-trois": 23,
    "vingt-cinq": 25,
    "trente": 30,
    "trente-cinq": 35,
    "quarante": 40,
    "quarante-cinq": 45,
    "cinquante": 50,
    "cinquante-cinq": 55,
    "": 0
}

def get_metas(p):
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    p_text = p.get_text()
    p_text = p_text.replace('\xa0', ' ')
    p_low = p_text.lower()
    if not commission and (p_low.find('commission') == 0 or p_low.find('délégation') == 0 or p_low.find('mission') == 0 or p_low.find('office') == 0 or p_low.find('comité') == 0 or p_low.find("groupe d") == 0):
        commission = p_text
        commission = re.sub(r'^Commission des affaires sociales (Mission)', r'\1', commission, re.I)
        commission = re.sub(r'^(Groupe de travail n|GROUPE DE TRAVAIL N)°[\s\d«–]+', r'Groupe de travail sur ', commission, re.I)
        commission = commission.replace("Groupe de travail sur PROCÉDURE LÉGISLATIVE ET ORGANISATION PARLEMENTAIRE ET DROITS DE L'OPPOSITION", "Groupe de travail sur la procédure législative et l'organisation parlementaire et les droits de l'opposition")
        commission = commission.strip(" »")
    for wday in ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'] + list(mois2nmois.keys()):
        if p_text.lower().find(wday) >= 0:
            try:
                days = re.findall(r'(\d+)e?r? *([^ \d]+) +(\d+)', p_text)
                if len(days) > 0:
                    date = "%04d-%02d-%02d" % (int(days[0][2]), mois2nmois[days[0][1].lower()], int(days[0][0]))
                    if mois2nmois[days[0][1].lower()] > 8:
                        session = days[0][2]+str(int(days[0][2]) + 1)
                    else:
                        session = str(int(days[0][2]) - 1)+days[0][2]
                    break
            except KeyError:
                continue
    if not heure and (p_text.lower().find(' heure') > -1 or p_text.find(' h ') > -1):
        heures = re.findall(r'(\d+) *h(?:eures?)? *(\d*)', p_text.lower())
        if len(heures) > 0 and heures[0][0]:
            heure = "%02d:" % int(heures[0][0])
            if len(heures[0]) > 1 and heures[0][1]:
                heure += "%02d" % int(heures[0][1])
            else:
                heure += '00'
            return
        heures = re.search(r'(?:commence|ouverte) à ([a-z\- ]+) *heures? *([a-z\- ]+)?\.', p_text.lower())
        if heures:
            heure = "%02d:%02d" % (heures_str.get(heures.group(1).strip(), 0), heures_str.get(heures.group(2).strip(), 0))
            return
    if p_text.find('session ') == 0:
        i = p_text.find(' 20')
        session = p_text[i+1:].replace('-', '')
    session = session.strip()

def html2json(s):
    global commission, date, heure, session, source, intervenant, intervention, timestamp
    soup = BeautifulSoup(s, features="lxml")
    p_tags = soup.find(class_="assnatSection1").find_all('p')
    source = source_url

    # Meta
    for p in p_tags:
        get_metas(p)
    extra = soup.find(class_="assnatSection2")
    if extra and not (commission and heure and date):
        p_tags = soup.find(class_="assnatSection2").find_all('p')
        for i, p in enumerate(p_tags):
            get_metas(p)
            if i > 10:
                break
    if not (commission and heure and date):
        print("ERROR: "+ sys.argv[1]+" for " + sys.argv[2] + " n'a pas de nom de commission ("+commission+"), date ("+date+") et/ou heure ("+heure+") identifiables dans la section assnatSection1. Merci de les ajouter à la main", file=sys.stderr)
        exit(2)

    # Interventions
    section = soup.find(class_="assnatSection2") or soup.find(class_="assnatSection1")
    if not section:
        print("ERROR: "+ sys.argv[1]+" for " + sys.argv[2] + " n'a pas de section assnatSection2 permettant d'identifier le corps du compte-rendu. Merci de l'ajouter à la main", file=sys.stderr)
        exit(2)
    p_tags = section.find_all(['p', 'h1', 'h2', 'h3', 'h3', 'table'], recursive=False)
    extras = soup.find(class_="assnatSection3")
    if extras:
        p_tags += extras.find_all(['p', 'h1', 'h2', 'h3', 'h3', 'table'], recursive=False)
    extras = soup.find(class_="assnatSection4")
    if extras:
        p_tags += extras.find_all(['p', 'h1', 'h2', 'h3', 'h3', 'table'], recursive=False)

    intervention = ''
    infos_commission = False
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
        if b and not infos_commission:
            if (hasPrefixIntervenant(b.get_text()) and b.get_text() != p.get_text()) or (not b.get_text().find('amendement') and b.get_text().find(' (') and b.get_text()[-2:] == ').'):
                new_intervention()
                intervenant = b.get_text()
                b.clear()
                b.unwrap()
            else:
                b_str = str(b)
                if b_str.find('</b>') > 0 and b_str[b_str.find('</b>'):] == '</b>' and len(b_str) > 8 and b_str.find('<b>') < 100:
                    new_intervention()
                    if (b_str.find(')</b>') > 0 or b_str.find(').</b>') > 0) and not str(p).find('</b></p>') > 0 and not re.search(r'\d', str(p)):
                        intervenant = b.get_text()
                        b.clear()
                        b.unwrap()
        if "Informations relatives à la commission" in p.get_text():
            infos_commission = True
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
        if (p_str.find('<p>–') == 0 or p_str.find('<p>----') == 0 or p_str.find('<p>__') == 0 or p_str.find('<p><i>—') == 0 or p_str.find('<p><i>__') == 0 or p_str.find('<p><b>–') == 0 or p_str.find('<p>—') == 0 or p_str.find('<p><b>—') == 0) and (p_str.find('–<') > 0 or p_str.find('----<') > 0 or p_str.find('_<') > 0 or p_str.find('—<') > 0):
            new_intervention()
            continue
        if p_str.find('<p>*</p>') == 0 :
            if intervenant:
                new_intervention()
        has_video = p_str.find('videos.assemblee-nationale.fr') >= 0 or p_str.find('assnat.fr') >= 0
        if p_str.find('<i>(') > 0 and p_str.find(')</i>') > 0 and not infos_commission:
            didascalie = re.findall(r'(.*)(<i>\([^)]*\)</i>)( *.? *</p>)', p_str)
            if didascalie:
                intervention += didascalie[0][0] + didascalie[0][2]
                oldintervenant = intervenant
                new_intervention()
                intervention = '<p>'+didascalie[0][1]+'</p>'
                new_intervention()
                intervenant = oldintervenant
                continue
        elif not has_video and not infos_commission and p_str.find('<p><i>') == 0 and (p_str.find('></p>') > 0 or p_str.find('>.</p>') > 0):
            if intervenant:
                new_intervention()
            p_str = p_str.replace('<i>', '')
            p_str = p_str.replace('</i>', '')
            intervention += p_str
            continue
        didascalie = re.search(r'(^.*[.…!?]+) *\(([^)]+)\) *(.*</p>)', p_str)
        if didascalie:
            intervention += didascalie.group(1) + '</p>'
            oldintervenant = intervenant
            new_intervention()
            intervention = '<p><i>(' + didascalie.group(2) + ')</i></p>'
            new_intervention()
            intervenant = oldintervenant
            intervention = '<p>' + didascalie.group(3)
            new_intervention()
            continue
        br = p.find('br')
        if br:
            br.unwrap()
        p = str(p)
        if has_video:
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
            response['url'] = response['url'].replace('Datas/an/portail/', '')
            video = re.findall(r'(https?://videos.assemblee-nationale.fr/video\.([^\.]*)(\.[^<"\']+|))', response['url'])
        try:
            videoid = video[0][1]
            urlvideo = video[0][0]
            urlvideo = re.sub('\??timecode=\d*', '', urlvideo)
            if len(urlvideo) >= 150:
                urlvideo = re.sub(r'(/video\.[^\.]*)\.[^?]*', r'\1', urlvideo)
            urlvideo_meta = "https://videos.assemblee-nationale.fr/Datas/an/%s/content/data.nvs" % videoid
            urlvideotimestamp = "https://videos.assemblee-nationale.fr/Datas/an/%s/content/finalplayer.nvs" % videoid
            response = requests_get(urlvideo_meta)
            soupvideo = BeautifulSoup(response['content'], features="lxml")
            response = requests_get(urlvideotimestamp)
            souptimestamp = BeautifulSoup(response['content'], features="lxml")
        except IndexError:
            soupvideo = None
            souptimestamp = None
            if '<h1 class="contentheading">Demande de vidéo</h1>' in response['content']:
                print("WARNING: compte-rendu %s has links to a disappeared video : %s " % (source, video or url), file=sys.stderr)
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
            urlthumbnail = "https://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, videotimestamp_thumbnail)
            imagethumbnail = requests_get(urlthumbnail)
            if imagethumbnail['content_type'] == 'error':
                urlthumbnail = "https://videos.assemblee-nationale.fr/Datas/an/%s/files/storyboard/%d.jpg" % (videoid, videotimestamp_thumbnail + 1)
                imagethumbnail = requests_get(urlthumbnail)
            if imagethumbnail and imagethumbnail['content_type'] != 'error':
                imagehtmlthumbnail = "<img src='data:%s;base64,%s'/>" % (imagethumbnail['content_type'], imagethumbnail['content'])
        new_intervention()
        label = chapter.get('label')
        label = re.sub(r'^\s', '', label)
        label = re.sub(r'\s*$', '', label)
        label = re.sub(r'\s+', ' ', label)
        if hasPrefixIntervenant(label):
            intervenant = label
            intervention = ''
        else:
            intervention = "<p><h4>"+label+"</h4></p>"
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
    #{"commission": "commission des finances, de l'économie générale et du contrôle budgétaire", "intervention": "<p>Présidence de M. Éric Woerth, Président</p>", "date": "2020-01-15", "source": "https://www.assemblee-nationale.fr/15/cr-cfiab/19-20/c1920037.asp#P9_450", "heure": "09:30", "session": "20192020", "intervenant": "", "timestamp": "37000020"}
    intervenant = intervenant.replace('\xa0', ' ')
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

    intervention = re.sub(r'<a id="[^"]*">([^<]*)</a>', r'\1 ', intervention)
    intervention = re.sub(r'([^> ])<b>', r'\1 <b>', intervention)
    intervention = re.sub(r'</b>([^< \.,])', r'</b> \1', intervention)
    intervention = re.sub(r'([^> ])<i>', r'\1 <i>', intervention)
    intervention = re.sub(r'</i>([^< \.,])', r'</i> \1', intervention)
    intervention = re.sub(r'(<i>[^<]*)<i>\s*', r'\1', intervention)
    intervention = re.sub(r'\s*</i>([^<]*</i>)', r'\1', intervention)
    intervention = re.sub(r' style="[^"]+"', r' ', intervention)
    intervention = re.sub(r'([a-z])É([a-z])', r'\1é\2', intervention)
    intervention = re.sub(r'([a-z])É([a-z])', r'\1é\2', intervention)
    intervention = re.sub(r'([a-z])È([a-z])', r'\1è\2', intervention)
    intervention = re.sub(r'([a-z])È([a-z])', r'\1è\2', intervention)
    intervention = re.sub(r'([a-z])Ê([a-z])', r'\1ê\2', intervention)
    intervention = re.sub(r'([a-z])Ê([a-z])', r'\1ê\2', intervention)
    intervention = re.sub(r'([a-z]) À ([a-z])', r'\1 à \2', intervention)
    intervention = re.sub(r'<p[^>]*>', '<p>', intervention)
    intervention = re.sub(r'<p>[: ]*', '<p>', intervention)
    intervention = re.sub(r'(<p>[….\s]*</p>\s*)+', '', intervention)

    intervention = re.sub(r'<t(able|head|body|r|h|d)\s+>', r'<t\1>', intervention)
    intervention = re.sub(r'<(t(able|head|body|r|h|d)|p)>\s+<(t(able|head|body|r|h|d)|p)>', r'<\1><\3>', intervention)
    intervention = re.sub(r'</(t(able|head|body|r|h|d)|p)>\s+</(t(able|head|body|r|h|d)|p)>', r'</\1></\3>', intervention)
    intervention = re.sub(r'</t([rdh])>\s+<t\1>', r'</t\1><t\1>', intervention)

    if len(intervention) > 200000:
        intervention = re.sub(r'<p><img [^>]*></p>', '<p><i>(image non chargée)</i></p>', intervention)
    while len(intervention) > 100000:
        fin_p = 30000 + intervention[30000:].find('</p>') + 4
        intervention1 = intervention[0:fin_p]
        intervention2 = intervention[fin_p:]
        if fin_p > 100000:
            break
        intervention = intervention1
        new_intervention()
        intervention = intervention2
        new_intervention()

    [intervenant, fonction] = getIntervenantFonction(intervenant)
    if intervention:
        timestamp += 10
        curtimestamp = timestamp
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
        if intervenant and not fonction.find('résident') > 0:
            if intervenant.find(' ') >= 0 :
                prenom = intervenant[0:intervenant.find(' ') - 1]
                if not prenom in prefixes_noms:
                    prefixes_noms.append(prenom)
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
    intervenant = re.sub(r'[.\s]+[\-–‑]?$', '', intervenant)
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
    if len(intervenantfonction) > 0 and not intervenantfonction[0][0].lower().find('président') >= 0:
        [intervenant, fonction] = intervenantfonction[0]
    # cleanup parenthesis from intervenant (groupe) after having separated fonction
    intervenant = re.sub(r'\s+\(\s*[A-ZÉ][\w\s\-]+\)$', '', intervenant)
    prez = re.findall(r'([^,<]*président?c?e?(?: d\'âge)?|c?o?-?rapporteure?)[,;]? (..[^\.,;]*)([,;] [^\.]*)?', intervenant, re.IGNORECASE)
    if prez and prez[0][1].find('général') != 0:
        [fonction2, intervenant, fonction3] = prez[0]
        if fonction:
            fonction = fonction2 + ', ' + fonction + fonction3
        else:
            fonction = fonction2 + fonction3
    intervenant = re.sub(r"^M[.me]+ ", "", intervenant, re.I)
    intervenant = re.sub(r"Monica Michel$", "Monica Michel-Brassart", intervenant, re.I)
    intervenant = re.sub(r"Audrey Dufeu.?Schubert", "Audrey Dufeu", intervenant, re.I)
    intervenant = re.sub(r"Florence Lasserre.?David", "Florence Lasserre", intervenant, re.I)
    intervenant = re.sub(r"Charlotte Lecocq", "Charlotte Parmentier-Lecocq", intervenant, re.I)
    if not fonction and intervenant2fonction.get(intervenant):
        fonction = intervenant2fonction[intervenant]
    elif fonction:
        fonction = re.sub(r'^(,|la?e?)\s+', '', fonction)
        if intervenant_sexe == "|F|":
            if fonction.find("président ") >= 0 or fonction.endswith("président"):
                fonction = fonction.replace("président", "présidente")
            elif fonction.find("rapporteur ") >= 0 or fonction.endswith("rapporteur"):
                fonction = fonction.replace("rapporteur", "rapporteure")
        elif intervenant_sexe == "|H|":
            if fonction.find("présidente ") >= 0 or fonction.endswith("présidente"):
                fonction = fonction.replace("présidente", "président")
            elif fonction.find("rapporteure ") >= 0 or fonction.endswith("rapporteure"):
                fonction = fonction.replace("rapporteure", "rapporteur")
        intervenant2fonction[intervenant] = fonction
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

use_cache = "--use-cache" in sys.argv
if use_cache:
    sys.argv.remove("--use-cache")
content_file = sys.argv[1]
source_url = sys.argv[2]
with open(content_file, encoding='utf-8') as f:
    raw_html = f.read()
    html = cleanhtml(raw_html)
    html2json(html)
