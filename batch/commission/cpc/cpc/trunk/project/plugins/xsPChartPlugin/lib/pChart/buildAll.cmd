ECHO OFF
CLS
ECHO Processing all examples
ECHO.
ECHO  [01/22] A simple line chart
 php -q %~dp0Example1.php
ECHO  [02/22] A cubic curve graph
 php -q %~dp0Example2.php
ECHO  [03/22] An overlayed bar graph
 php -q %~dp0Example3.php
ECHO  [04/22] Showing how to draw area
 php -q %~dp0Example4.php
ECHO  [05/22] A limits graph
 php -q %~dp0Example5.php
ECHO  [06/22] A simple filled line graph
 php -q %~dp0Example6.php
ECHO  [07/22] A filled cubic curve graph
 php -q %~dp0Example7.php
ECHO  [08/22] A radar graph
 php -q %~dp0Example8.php
ECHO  [09/22] Showing how to use labels
 php -q %~dp0Example9.php
ECHO  [10/22] A 3D exploded pie graph
 php -q %~dp0Example10.php
ECHO  [11/22] A true bar graph
 php -q %~dp0Example12.php
ECHO  [12/22] A 2D exploded pie graph
 php -q %~dp0Example13.php
ECHO  [13/22] A smooth flat pie graph
 php -q %~dp0Example14.php
ECHO  [14/22] Playing with line style and pictures inclusion
 php -q %~dp0Example15.php
ECHO  [15/22] Importing CSV data
 php -q %~dp0Example16.php
ECHO  [16/22] Playing with axis
 php -q %~dp0Example17.php
ECHO  [17/22] Missing values
 php -q %~dp0Example18.php
ECHO  [18/22] Error reporting
 php -q %~dp0Example19.php
ECHO  [19/22] Stacked bar graph
 php -q %~dp0Example20.php
ECHO  [20/22] Naked and easy!
 php -q %~dp0Naked.php
ECHO  [21/22] Let's go fast, draw small!
 php -q %~dp0SmallGraph.php
ECHO  [22/22] A Small stacked chart
 php -q %~dp0SmallStacked.php
ECHO.
ECHO Rendering complete!
PAUSE
