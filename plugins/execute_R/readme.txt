execute_R v. 2.1
For Pumilio 2.1.0 or higher.

This is a plugin for Pumilio. Just drop it in the plugins/ folder so that you end up with:
 [pumilioroot]/plugins/execute_R.php
 [pumilioroot]/plugins/execute_R/

The plugin will check that R is installed and will check if the needed packages are installed.
The packages 'tuneR', 'seewave' and 'RMySQL' are required. Also you should install fftw for the
 most recent versions of seewave:
	sudo apt-get install fftw3*

