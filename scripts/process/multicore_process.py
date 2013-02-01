#!/usr/bin/python
#For Pumilio 2.1.0 or more recent

"""
v. 2.1.0a (16 Nov 2011)
Main script to trigger several instances of the pumilio_process.py script using  
 standard modules.
It gets the number of cores in the computer and launches the same number of 
  instances of the script pumilio_process.py in their own directory. On cancel (Ctrl-C)
  it will kill all the instances and clean up.
To use less than all the cores, uncomment the line below:
	no_cores = no_cores - 1
   and edit accordingly.
"""

#Example from
#http://pyinsci.blogspot.com/2009/02/usage-pattern-for-multiprocessing.html

import multiprocessing
import random
import shutil
import os
import time
import subprocess
import signal

this_dir = os.getcwd()

#Get number of cores
no_cores = multiprocessing.cpu_count()

#uncomment below and change to NOT use all the cores available
#no_cores = no_cores - 1

def newprocess():
	this_random_folder = str(int(random.random()*1000000))
	os.mkdir(this_random_folder)
	shutil.copy("configfile.py", this_random_folder)
	shutil.copy("pumilio_process.py", this_random_folder)
	return this_random_folder

def runprocess(folder):
	os.chdir(folder)
	p = subprocess.Popen(["python", "pumilio_process.py"])
	os.chdir(this_dir)
	return p

def killprocess(child_process, folder):
	#child_process.terminate()
	child_process.send_signal(signal.SIGINT)
	#os.kill(signal.CTRL_C_EVENT, child_process)
	shutil.rmtree(folder, ignore_errors=True)
	return

try:

	folders = []
	children = []

	for index in range(no_cores):
		folders.append(newprocess())
		children.append(runprocess(folders[index]))
		os.chdir(this_dir)
		#Delay to avoid clashes
		time.sleep(5)

	while True:
		time.sleep(1)

except (KeyboardInterrupt):
	print "\n\n Interrupt command received...\n  cleaning up, please wait..."

	for index in range(len(children)):
		killprocess(children[index], folders[index])

	print " To restart analysis, type:  ./multicore_process.py\n  and press [ENTER]\n\n"

except (SystemExit):
	print "\n\n Ending program...\n  cleaning up, please wait..."

	for index in range(len(children)):
		killprocess(children[index], folders[index])

	print " To restart analysis, type:  ./multicore_process.py\n  and press [ENTER]\n\n"

except Exception as inst:
	print "\n\n Error, cleaning up, please wait..."

	print "\n\n Interrupt command received...\n  cleaning up, please wait..."

	for index in range(len(children)):
		killprocess(children[index], folders[index])

	print " To restart analysis, type:  ./multicore_process.py\n  and press [ENTER]\n\n"

