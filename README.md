pumilio
=======

Pumilio is a PHP/MySQL application that serves as a sound archive manager
 and allows the user to load sound files in many formats and see the
 spectrogram of the sound, select regions of the sound for further
 analysis and insertion in a database, filter, and many other manipulations.

http://pumilio.sourceforge.net

Copyright (©) 2010-2013 Luis J. Villanueva-Rivera (ljvillanueva@coquipr.com)
Licensed under the GPLv3

Citation: Villanueva-Rivera, Luis J., and Bryan C. Pijanowski. 2012.
 Pumilio: A Web-Based Management System for Ecological Recordings.
 Bulletin of the Ecological Society of America 93: 71-81.
 doi: [10.1890/0012-9623-93.1.71](http://dx.doi.org/10.1890/0012-9623-93.1.71)
 

##Folder structure

* www - contains the files that go in the webserver (i.e. /var/www/)
* scripts - contains helper scripts that are run from the command line
* tools - optional scripts that can be added to the www/tools/ folder to expand the visualization tool
* plugins - optional scripts that are shown in the main menu

##Thanks to

This application uses several other open source tools and programs like:

* Blueprint CSS framework (http://blueprintcss.org/)
* SoX (http://sox.sourceforge.net/)
* wav2png script by Freesound.org (http://www.freesound.org/blog/?p=10)
* JW Player (http://www.longtailvideo.com/players/jw-flv-player/)
* Flot (http://www.flotcharts.org/)
* JPlayer (http://www.jplayer.org)
* Audiolab Python module (http://www.ar.media.kyoto-u.ac.jp/members/david/softwares/audiolab/sphinx/index.html)
* JQuery Javascript library (http://www.jquery.com/)
* JCrop image cropping plugin (http://deepliquid.com/content/Jcrop.html)
* DByte (http://github.com/Xeoncross/DByte)
* Plupload (http://www.plupload.com)
* Google Maps (http://maps.google.com)
* Google Web Fonts (http://www.google.com/webfonts)
* Ajax wait icons by Andrew B. Davidson (http://www.andrewdavidson.com/articles/spinning-wait-icons/)
* Crystal Project icons (http://everaldo.com/crystal/?action=downloads)
* Silk icons (http://www.famfamfam.com/lab/icons/silk/)

The name "pumilio" refers to the tropical frog known as the Strawberry Poison-dart
 Frog (Oophaga pumilio), a bold little critter that sings during the day without
 fear of being predated upon.

Feel free to contribute code, ideas, suggestions, bugs, etc.

Read the INSTALLATION file to install and the UPGRADE file to upgrade.

To prevent access to the original sound files from the web (for example, someone trying to 
 download your whole dataset), rename the file "htaccess" to ".htaccess" (starting with a period)
 and place it in the main folder of the application.
 The apache server must be configured to allow this option. 

 In Ubuntu:
 ```bash
  sudo a2enmod rewrite
  sudo service apache2 restart
  ```
 For other distros or options, please consult your administrator.
 
You can also visit the project page for more information: 
 http://pumilio.sourceforge.net

