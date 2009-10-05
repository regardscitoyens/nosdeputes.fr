#!/usr/bin/python

import os
import re
import sys
import xml.dom.minidom
import string

import parse_path

if len(sys.argv) == 1:
    sys.exit("svn2imagemap.py FILENAME dptmt")
if not os.path.exists(sys.argv[1]):
    sys.exit("Input file does not exist")

svg_file = xml.dom.minidom.parse(sys.argv[1])
svg = svg_file.getElementsByTagName('svg')[0]


elements = [g for g in svg.getElementsByTagName('g')]
border = 10.

xmin = float(svg.attributes['width'].value)
ymin = float(svg.attributes['height'].value)
xmax = ymax = 0.0

for e in elements:
    x_shift = 0.
    y_shift = 0.
    for t in e.getElementsByTagName('text'):
        t.parentNode.removeChild(t)
    if e.hasAttribute('transform'):
        for transform in re.findall(r'(\w+)\((-?\d+.?\d*),(-?\d+.?\d*)\)', e.getAttribute('transform')):
            if transform[0] == 'translate':
                x_shift = float(transform[1])
                y_shift = float(transform[2])
    for path in e.getElementsByTagName('path'):
        if re.match(sys.argv[2]+"-\d\d", path.attributes['id'].value) == None:
            path.parentNode.removeChild(path)
        else: 
            (x0, y0, x1, y1) = parse_path.get_limits(path.getAttribute('d'))
            xmin = min(xmin, x0 + x_shift)
            ymin = min(ymin, y0 + y_shift)
            xmax = max(xmax, x1 + x_shift)
            ymax = max(ymax, y1 + y_shift)

xmin -= border
ymin -= border
xmax += border
ymax += border

for e in elements:
    x_shift = -xmin
    y_shift = -ymin
    if e.hasAttribute('transform'):
        for transform in re.findall(r'(\w+)\((-?\d+.?\d*),(-?\d+.?\d*)\)', e.getAttribute('transform')):
            if transform[0] == 'translate':
                x_shift += float(transform[1])
                y_shift += float(transform[2])
    e.setAttribute('transform',"translate("+str(x_shift)+","+str(y_shift)+")")
    
svg.attributes['width'].value = str(int(xmax-xmin))
svg.attributes['height'].value =  str(int(ymax-ymin))


outfile = open("svg/"+sys.argv[2]+".svg", 'w')
outfile.write(svg_file.toxml().encode('utf-8'))
outfile.close()

os.system ("inkscape -d 135 -e png/" + sys.argv[2] + ".png " 
+ " " +"svg/"+sys.argv[2]+".svg")
