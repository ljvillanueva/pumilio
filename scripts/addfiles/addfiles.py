#!/usr/bin/python
# For Pumilio 2.1.0 or recent
"""

v. 2.1.0c (18 Jul 2012)
Script to insert sound files in FLAC format for Pumilio.
 Edit the file configfile.py.dist with the appropiate values and save it as configfile.py

The script is made for sounds recorded using WildlifeAcoustics SongMeters automated recorders.
 The filename is assumed to be:
 *YYYYMMDD_HHMMSS.wav
 Where * is the prefix for the recorder.

The script assumes it can write on the sounds/ directory of Pumilio. It should be a local folder
 or be mounted locally, for example using CIFS, SSHFS, NFS, etc and this computer must be able
 to write on it.

The original files will not be deleted.

Whenever there is an error, the script will try to give information on what the problem was, 
 pay attention at these messages.
 
Version c asks for the sensor used.
"""

#########################################################################
# HEADER DECLARATIONS							#
#########################################################################

from __future__ import with_statement

# Import modules
import commands
import os
import wave
import sys
import threading
import datetime
import time
import shutil
import hashlib
import random

try:
	import MySQLdb
except:
	print "\n MySQLdb is not installed. To install in Ubuntu use: \n   sudo apt-get install python-mysqldb\n"
	sys.exit (1)


#########################################################################
# COMMAND LINE ARGUMENTS						#
#########################################################################
	
# Test "global" variables
if len(sys.argv) < 2:
	print "\n Incorrect number of arguments given."
	print "\n Call the program as: " + sys.argv[0] + " <directory with the files>"
	print "\n Optional values are: <Collection ID> <Site ID> <Sensor ID>\n"
	print "\n Exiting program.\n"
	sys.exit(1)

# Place "global" variables in the namespace
try:
	from configfile import *
except:
	print "\n The configuration file is missing.\n  Rename the file configfile.py.dist to configfile.py\n  and fill the values.\n"
	sys.exit (1)


#ColID = sys.argv[1]
dir_to_process = sys.argv[1]

#check that can read folder
if os.access(dir_to_process, os.W_OK) == False:
	print "\n The directory with the files '" + server_dir + "' does not exist or do not have write permissions."
	print "\n Exiting program.\n"
	sys.exit(1)

#check that can write server folder
if os.access(server_dir + 'sounds/', os.W_OK) == False:
	print "\n The server directory '" + server_dir + 'sounds/' + "' does not exist or do not have write permissions."
	print "\n Check the settings in configfile.py and try again."
	print "\n Exiting program.\n"
	sys.exit(1)

#Change working dir
cur_dir=os.getcwd()
#Change working dir to dir with files
#commands.getstatusoutput('cp svt.py ' + dir_to_process + '/')
os.chdir(dir_to_process)


if len(sys.argv) > 2:
	ColID = sys.argv[2]
	siteid = sys.argv[3]
	sensorid = sys.argv[4]


#########################################################################
# FUNCTION DECLARATIONS							#
#########################################################################

# Implementation of Ticker class
# Creates a progress bar made of points to indicate that the script is working
class Ticker(threading.Thread):
    def __init__(self, msg):
	threading.Thread.__init__(self)
	self.msg = msg
	self.event = threading.Event()
    def __enter__(self):
	self.start()
    def __exit__(self, ex_type, ex_value, ex_traceback):
	self.event.set()
	self.join()
    def run(self):
	sys.stdout.write(self.msg)
	while not self.event.isSet():
	    sys.stdout.write(".")
	    sys.stdout.flush()
	    self.event.wait(1)


#Extract wav from FLAC
def extractflac(item_flac):
	item_wav = item_flac[:-5] + '.wav'
	status, output = commands.getstatusoutput('flac -dFf ' + item_flac + ' -o ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	return item_wav


def fileExists(f):
	try:
		file = open(f)
	except IOError:
		exists = 0
	else:
		exists = 1
	return exists
	
def cleanup(server_dir,ColID,DirID,item_wav,item_flac):
	item_prefix=item_flac[:-5]
	ColID=str(ColID)
	pathToUse = server_dir
	pathToSound = pathToUse + 'sounds/sounds/' + ColID + '/' + DirID + '/'
	#
	if os.path.exists(pathToUse)==0:
		print "\n \n Could not find the sounds/ directory. Check your settings and try again.\n"
		sys.exit(1)
	#	
	if os.path.exists(pathToUse + 'sounds/sounds/' + ColID)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToUse + 'sounds/sounds/' + ColID)
		if status != 0:
			print " ERROR: Could not create necessary folder " + pathToUse + 'sounds/sounds/' + ColID
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod -R 777 ' + pathToSound)
	if os.path.exists(pathToSound)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToSound)
		if status != 0:
			print " ERROR: Could not create necessary folder " + pathToSound
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod -R 777 ' + pathToSound)
	#
	#Move the already processed file to a done folder
	status, output = commands.getstatusoutput('cp ' + item_flac + ' ' + pathToSound)
	if status != 0:
		print " ERROR: Could not copy the file " + item_flac + " to the server directory"
		print output
		sys.exit(1)
	#status, output = commands.getstatusoutput('chmod 777 ' + pathToSound + item_flac)
	#if status != 0:
	#	print " ERROR: Could not change the permissions of the file " + item_flac
	#	print output
	#	sys.exit(1)
	os.remove(item_wav)
	return


def getmd5(flac_file):
	"""
	Getting the MD5 hash for the file.
	"""
	f1 = file(flac_file ,'rb')
	m = hashlib.md5()
	while True:
		t = f1.read(1024)
		if len(t) == 0: break
		m.update(t)
	return m.hexdigest()


def insert(ColID, soundname, mysql_name, soundformat, no_channels, samplingrate, bitres, soundlength, sounddate, soundtime, file_md5, filesize, siteid, DirID, sensorid):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "INSERT INTO Sounds (ColID, SoundName, OriginalFilename, SoundFormat, Channels, SamplingRate, BitRate, Duration, Date, Time, MD5_hash, FileSize, SiteID, DirID, SensorID) \
         VALUES (" + \
	`ColID` + ', ' + `soundname` + ', ' + `mysql_name` + ', ' + `soundformat` + ', ' + `no_channels` + ', ' + `samplingrate` + ', ' + `bitres` + ', ' + `soundlength` + ', ' + `sounddate` + ', ' + `soundtime` + ', ' + `file_md5` +  ', ' + `filesize` + ', ' + `siteid` + ', ' + `DirID` + ', ' + `sensorid` + ')'
	#print "Query: " + query + "\n"
	cursor.execute (query)
	SoundID=con.insert_id()
	#Close MySQL
	cursor.close ()
	con.close ()
	return str(SoundID)


def open_wave(file_wav):
	"""
	Open the wave file specified in the command line or elsewhere for processing.
	"""
	wave_pointer = wave.open(file_wav,'rb')
	return wave_pointer


def find_values(wave_pointer):
	"""
	Read the values to fill the "wave_vars" array from the sound file.
	"""
	wave_vars = {}
	wave_vars['samp_rate'] = wave_pointer.getframerate()
	wave_vars['num_samps'] = wave_pointer.getnframes()
	wave_vars['samp_width'] = wave_pointer.getsampwidth()
	wave_vars['no_channels'] = wave_pointer.getnchannels()
	if wave_vars['samp_width'] == 1:
		# The data are 8 bit unsigned
		wave_vars['bit_code'] = 'B'
		wave_vars['bits'] = '8'
	elif wave_vars['samp_width'] == 2:
		# The data are 16 bit signed
		wave_vars['bit_code'] = 'h'
		wave_vars['bits'] = '16'
	elif wave_vars['samp_width'] == 4:
		# The data are 32 bit signed
		wave_vars['bit_code'] = 'i'
		wave_vars['bits'] = '32'
	else:
		# I don't know what the hell it is
		print "I don't know what the hell bit width you're using."
		sys.exit()
	wave_vars['max_time'] = wave_vars['num_samps'] / wave_vars['samp_rate']
	# Print wave file values, mostly to debug
	#print "Wave values: "
	#for item in wave_vars.iteritems():
	#	print item
	return wave_vars


#Insert data to MySQL
def tomysql(ColID, item_wav, item_flac, file_md5, siteid, DirID, sensorid):
	filesize=os.path.getsize(item_flac)
	#mp3filename=item_flac[:-5] + '.mp3'
	#
	#Convert filename to date in format YYYY-MM-DD
	SampDate = item_flac[-20:-16] + "-" + item_flac[-16:-14] + "-" + item_flac[-14:-12]
	#Convert filename to time in format HH:MM
	SampTime = item_flac[-11:-9] + ":" + item_flac[-9:-7]
	#
	wave_pointer = open_wave(item_wav)
	wave_vars = find_values(wave_pointer)
	soundname=item_flac[:-5]
	SoundID=insert(ColID, soundname, item_flac, 'flac', wave_vars['no_channels'], wave_vars['samp_rate'], wave_vars['bits'], wave_vars['max_time'], SampDate, SampTime, file_md5, str(filesize), siteid, DirID, sensorid)
	print "\n  MySQL Insert was successful"
	return SoundID


def getsites():
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SiteID, SiteName, SiteLat, SiteLon, SiteNotes FROM Sites ORDER BY SiteName";
        cursor.execute (query)
        rows = cursor.fetchall ()
        print " Sites: "
        print " ID\tName of site and coordinates\n====================================\n"
        for row in rows:
                print " %s\t%s (%s,%s) (%s)" % (row[0], row[1], row[2], row[3], row[4])
        print " "
        cursor.close ()
        con.close ()


def getsensors():
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SensorID, Recorder, Microphone, Notes FROM Sensors ORDER BY SensorID";
        cursor.execute (query)
        rows = cursor.fetchall ()
        print " Sensors: "
        print " ID\tSensor and notes\n====================================\n"
        for row in rows:
                print " %s\t%s - %s. %s" % (row[0], row[1], row[2], row[3])
        print " "
        cursor.close ()
        con.close ()



def confirmsite(siteid):
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SiteName, SiteLat, SiteLon, SiteNotes FROM Sites WHERE SiteID='" + siteid + "' LIMIT 1";
        cursor.execute (query)
        if cursor.rowcount==0:
                print "\n That ID does not exist... Leaving program\n"
                sys.exit(1)
        row = cursor.fetchone ()
        print "The site selected is: \n\n %s (%s,%s) (%s)" % (row[0], row[1], row[2], row[3])
        cursor.close ()
        con.close ()
        
        

def confirmsensor(sensorid):
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SensorID, Recorder, Microphone, Notes FROM Sensors WHERE SensorID='" + sensorid + "' LIMIT 1";
        cursor.execute (query)
        if cursor.rowcount==0:
                print "\n That ID does not exist... Leaving program\n"
                sys.exit(1)
        row = cursor.fetchone ()
        print "The sensor selected is: \n\n %s: %s, %s - %s" % (row[0], row[1], row[2], row[3])
        cursor.close ()
        con.close ()


def getsources():
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT ColID, CollectionName FROM Collections ORDER BY CollectionName";
        cursor.execute (query)
        rows = cursor.fetchall ()
        print " Sites: "
        print " ID\tName of collection\n====================================\n"
        for row in rows:
                print " %s\t%s" % (row[0], row[1])
        print " "
        cursor.close ()
        con.close ()


def confirmsource(colID):
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT CollectionName FROM Collections WHERE ColID='" + colID + "' LIMIT 1";
        cursor.execute (query)
        if cursor.rowcount==0:
                print "\n That ID does not exist... Leaving program\n"
                sys.exit(1)
        row = cursor.fetchone ()
        print "The collection selected is: \n\n %s" % (row[0])
        cursor.close ()
        con.close ()


#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

#Select a source for the sounds from the sites available in the database

try:
	ColID
except NameError:
	confirmid="n"
	while confirmid=="n":
        	getsources()
        	ColID = raw_input('\n\nEnter the ID of the collection to save the files into: ')
        	confirmsource(ColID)
        	while confirmid!="y":
        	        confirmid = raw_input('\nIs this the correct collection? [y/n]: ')
        	        if confirmid == "y" or confirmid == "n":
        	                if confirmid=='y': continue
        	                if confirmid=='n': break
        	        else:
        	                print "Error, please type the letter \"y\" for yes or \"n\" for no\n"

#Select a site for the sounds from the sites available in the database
try:
	siteid
except NameError:
	confirmid="n"
	while confirmid=="n":
        	getsites()
        	siteid = raw_input('\n\nEnter the ID of the site: ')
        	confirmsite(siteid)
        	while confirmid!="y":
        	        confirmid = raw_input('\nIs this the correct site? [y/n]: ')
        	        if confirmid == "y" or confirmid == "n":
        	                if confirmid=='y': continue
        	                if confirmid=='n': break
        	        else:
        	                print "Error, please type the letter \"y\" for yes or \"n\" for no\n"

#Select a sensor
try:
	sensorid
except NameError:
	confirmid="n"
	while confirmid=="n":
        	getsensors()
        	sensorid = raw_input('\n\nEnter the ID of the sensor: ')
        	confirmsensor(sensorid)
        	while confirmid!="y":
        	        confirmid = raw_input('\nIs this the correct sensor? [y/n]: ')
        	        if confirmid == "y" or confirmid == "n":
        	                if confirmid=='y': continue
        	                if confirmid=='n': break
        	        else:
        	                print "Error, please type the letter \"y\" for yes or \"n\" for no\n"


ls = os.listdir(os.getcwd())

try:
	for item_flac in ls:
		if item_flac[-5:] == ".flac" or item_flac[-5:] == ".FLAC":
			print '\nChecking file ' + item_flac

			#Get a random integer between 1 and 100
			DirID = str(random.randint(1, 100))

			#Get wav file to FLAC
			with Ticker("\n Opening FLAC..."):
				item_wav = extractflac(item_flac)
			print " "

			with Ticker("\n Getting MD5 hash of file..."):
				file_md5=getmd5(item_flac)
			print " "

			#
			with Ticker("\n Checking the file and inserting the data to MySQL..."):
				SoundID=tomysql(ColID, item_wav, item_flac, file_md5, str(siteid), DirID, str(sensorid))
			print " "
			with Ticker("\n Cleaning up..."):
				cleanup(server_dir, ColID, DirID, item_wav, item_flac)


except (KeyboardInterrupt):
	print "\n\n Interrupt command received...\n  cleaning up, please wait..."

	#Clean up directory
	status, output = commands.getstatusoutput('rm *.wav')

	when_stop=datetime.datetime.now().strftime("  Script keyboard-interrupted on %d/%b/%y %H:%M\n")
	print when_stop

	#Return to original dir
	os.chdir(cur_dir)

	status, output = commands.getstatusoutput('rm *.pyc')
	sys.exit (0) #Exit normally

	
#Return to original dir
os.chdir(cur_dir)

status, output = commands.getstatusoutput('rm *.pyc')

process_date = datetime.datetime.now().strftime("\n\n Folder completed on %d/%m/%Y at %I:%M %p\n")

print process_date
sys.exit (0)
