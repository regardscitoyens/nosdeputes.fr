#/bin/sh

FILE=$(printf %04d%02d%02d $3 $2 $1)
DATE=$(printf %02d/%02d/%04d $1 $2 $3)

if [ ! -d pdf ] ; then mkdir pdf; fi
if [ ! -d xml ] ; then mkdir xml; fi
rm jo.html  jo_ind.html  jo.pdf  jos.html 2> /dev/null

if [ ! -s pdf/$FILE.pdf ]
then
    echo $FILE donwload
    perl download_jo.pl $DATE jo.pdf
    if [ ! -e jo.pdf ] ; then exit; fi
    ln jo.pdf pdf/$FILE.pdf
else
    echo $FILE cached
    ln pdf/$FILE.pdf jo.pdf
fi
if [ -s xml/$FILE.xml ] ; then exit; fi
pdftohtml jo.pdf > /dev/null
if [ ! -e jos.html ] ; then exit; fi
perl parse_jo.pl jos.html > xml/$FILE.xml
if [ ! -s xml/$FILE.xml ] ; then rm xml/$FILE.xml ; fi
rm jo.html  jo_ind.html  jo.pdf  jos.html 2> /dev/null