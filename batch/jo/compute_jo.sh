#/bin/sh
#
#Usage : Jour Mois Année [NoPDF]
FILE=$(printf %04d%02d%02d $3 $2 $1)
DATE=$(printf %02d/%02d/%04d $1 $2 $3)
NOPDF=$4

if [ ! -d pdf ] ; then mkdir pdf; fi
if [ ! -d xml ] ; then mkdir xml; fi
rm jo.html  jo_ind.html  jo.pdf  jos.html 2> /dev/null

if [ ! -s pdf/$FILE.pdf ] && [ -z $NOPDF ]
then
    echo $FILE download
    perl download_jo.pl $DATE jo.pdf
    if [ ! -e jo.pdf ] ; then echo "ERROR jo.pdf" ; exit; fi
    ln jo.pdf pdf/$FILE.pdf
else if [ ! -s pdf/$FILE.pdf ] ; then echo "ERROR pdf/$FILE.pdf" exit ; fi
    echo $FILE cached
    ln pdf/$FILE.pdf jo.pdf
fi
#if [ -s xml/$FILE.xml ] ; then echo "ERROR xml/$FILE.xml"; exit; fi
pdftohtml jo.pdf > /dev/null
if [ ! -e jos.html ] ; then echo "ERROR jos.html" exit; fi
perl parse_jo.pl jos.html $DATE > xml/$FILE.xml
echo "xml/$FILE.xml created"
if [ ! -s xml/$FILE.xml ] ; then echo "ERROR JO empty"; rm xml/$FILE.xml ; fi
rm jo.html  jo_ind.html  jo.pdf  jos.html 2> /dev/null
