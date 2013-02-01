#!/usr/bin/python
# For Pumilio 2.4.0 or recent

"""
v. 2.4.0a (30 Jan 2013)
Script to export sound files from Pumilio.
 Edit the file configfile.py.dist with the appropiate values and save it as configfile.py

Whenever there is an error, the script will try to give information on what the problem was, 
 pay attention at these messages.
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
import types

try:
	import MySQLdb
except:
	print "\n MySQLdb is not installed. To install in Ubuntu use: \n   sudo apt-get install python-mysqldb\n"
	sys.exit (1)


#########################################################################
# COMMAND LINE ARGUMENTS						#
#########################################################################

# Place "global" variables in the namespace
try:
	from configfile import *
except:
	print "\n The configuration file is missing.\n  Rename the file configfile.py.dist to configfile.py\n  and fill the values.\n"
	sys.exit (1)

try:
	export_format
except NameError:
	print "\n The archive format has to be set up in the configfile.py file.\n"
	sys.exit (1)

if export_format == "tar" or export_format == "zip":
	print ""
else:
	print "\n The archive format has to be tar or zip.\n  Please check your options in the configfile.py file.\n"
	sys.exit (1)


#check that can read folder
if os.access(server_dir, os.R_OK) == False:
	print "\n The directory with the files '" + server_dir + "' does not exist or do not have read permissions."
	print "\n Exiting program.\n"
	sys.exit(1)


now = datetime.datetime.now()
export_dir = now.strftime("%Y%m%d_%H%M%S")


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


def fileExists(f):
	try:
		file = open(f)
	except IOError:
		exists = 0
	else:
		exists = 1
	return exists


def cleanup(export_dir, export_format):
	f = open(export_dir + "/columns.txt", 'a')
	f.write("Columns in the data.csv file:\n")
	f.write(" -SoundID: The unique ID of the file in the database\n")
	f.write(" -Filename: Filename of this file\n")
	f.write(" -Date: Date when the recording was made (in YYYY-MM-DD format)\n")
	f.write(" -Time: Time when the recording was made (in HH:MM:SS format)\n")
	f.write(" -SamplingRate: The sampling rate of the audio file\n")
	f.write(" -BitRate: The bitrate used in the audio file\n")
	f.write(" -Channels: The number of channels in the audio file\n")
	f.write(" -Duration: Duration of the audio file (in seconds)\n")
	f.write(" -FileFormat: Digital audio format of the audio file\n")
	f.write(" -SiteID: Unique ID of the site where the recording was made\n")
	f.write(" -SiteName: Name of the site where the recording was made\n")
	f.write(" -Latitude: Latitude of the site where the recording was made (in decimal degrees)\n")
	f.write(" -Longitude: Longitude of the site where the recording was made (in decimal degrees)\n")
	f.write(" -SoundNotes: Notes of this file\n")
	f.write(" -ColID: Unique ID of the collection where the file is archived\n")
	f.write(" -CollectionName: Name of the collection\n")
	f.write(" -CollectionNotes: Notes of the collection\n")
	f.write(" -Sensor_Recorder: Recorder used\n")
	f.write(" -Sensor_Microphone: Microphone used\n")
	f.write(" -Sensor_Notes: Notes on the recording equipment used\n")
	f.write(" -QualityFlagID: ID of the quality flag assigned to the file\n")
	f.write(" -QualityFlag: Text of the quality flag assigned to the file\n\n")
        f.close()
	if export_format=="zip":
		export_cmd = "zip -0 -r " + export_dir + ".zip " + export_dir
	if export_format=="tar":
		export_cmd = "tar -cvf " + export_dir + ".tar " + export_dir
	status, output = commands.getstatusoutput(export_cmd)
	shutil.rmtree(export_dir)
	return


def getsites():
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SiteID, SiteName, SiteLat, SiteLon FROM Sites ORDER BY SiteName";
        cursor.execute (query)
        rows = cursor.fetchall ()
        print " Sites: "
        print " ID\tName of site and coordinates\n====================================\n"
        for row in rows:
                print " %s\t%s (%s,%s)" % (row[0], row[1], row[2], row[3])
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
        query = "SELECT SiteName, SiteLat, SiteLon FROM Sites WHERE SiteID='" + siteid + "' LIMIT 1";
        cursor.execute (query)
        if cursor.rowcount==0:
                print "\n That ID does not exist... Leaving program\n"
                sys.exit(1)
        row = cursor.fetchone ()
        print "The site selected is: \n\n %s (%s,%s)" % (row[0], row[1], row[2])
        cursor.close ()
        con.close ()


def getcollections():
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


def confirmcollection(colID):
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


def exportcollection(colID, export_dir):
	export_dir = "col" +  "_" + colID + "_" + export_dir
	os.mkdir(export_dir)
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SoundID, ColID, DirID, OriginalFilename FROM Sounds \
			WHERE ColID='" + colID + "' AND SoundStatus!='9'"
        cursor.execute (query)
        file_rows = cursor.fetchall ()
        f = open(export_dir + "/data.csv", 'a')
	f.write("SoundID,Filename,Date,Time,SamplingRate,BitRate,Channels,Duration,FileFormat,")
	f.write("SiteID,SiteName,Latitude,Longitude,SoundNotes,ColID,CollectionName,")
	f.write("CollectionNotes,Sensor_Recorder,Sensor_Microphone,Sensor_Notes,QualityFlagID,QualityFlag\n")
	for file_row in file_rows:
		SoundID = str(file_row[0])
		filename = file_row[3]
	        thisfile = server_dir + "sounds/sounds/" + str(file_row[1]) + "/" + str(file_row[2]) + "/" + filename
        	shutil.copyfile(thisfile, export_dir + "/" + filename)
        	
        	#get sound info
        	query1 = "SELECT Sounds.OriginalFilename, Sounds.Date, TIME_FORMAT(Sounds.Time, '%H:%i:%S'), \
        			Sounds.SamplingRate, Sounds.BitRate, Sounds.Channels, \
				Sounds.Duration, Sounds.SoundFormat, Sounds.SiteID, \
				Sites.SiteName, Sites.SiteLat, Sites.SiteLon, Sounds.Notes, \
				Collections.ColID, Collections.CollectionName, Collections.Notes \
				FROM Sounds, Sites, Collections \
				WHERE Sounds.ColID=Collections.ColID AND \
				Sounds.SiteID=Sites.SiteID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1"
		cursor.execute (query1)
		if cursor.rowcount==1:
			sound_row = cursor.fetchone ()
	
		#if isinstance(sound_row[12], types.NoneType):
		
        	#get recorder info
        	query1 = "SELECT Sensors.Recorder, Sensors.Microphone, Sensors.Notes \
				FROM Sounds, Sensors \
				WHERE Sounds.SensorID = Sensors.SensorID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1"
		cursor.execute (query1)
		if cursor.rowcount==1:
			recorder_row = cursor.fetchone ()
		else:
			recorder_row = ["None", "None", "None"]

        	#get qualflags
        	query2 = "SELECT QualityFlags.QualityFlagID, QualityFlags.QualityFlag \
				FROM Sounds, QualityFlags \
				WHERE Sounds.QualityFlagID=QualityFlags.QualityFlagID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1";
		cursor.execute (query2)
		if cursor.rowcount==1:
			qual=True;
			quality_row = cursor.fetchone ()
    	        f.write(SoundID + ",\"" + sound_row[0] + "\"," + str(sound_row[1]) + "," + str(sound_row[2]) + "," + str(sound_row[3]) + "," + str(sound_row[4]) + "," + str(sound_row[5]) + "," + str(sound_row[6]) + "," + sound_row[7] + "," + str(sound_row[8]) + ",\"" + sound_row[9] + "\"," + str(sound_row[10]) + "," + str(sound_row[11]) + "," + str(sound_row[12]) + ",\"" + str(sound_row[13]) + "\"," + sound_row[14] + ",\"" + str(sound_row[15]) + "\",\"" + str(recorder_row[0]) + "\",\"" + str(recorder_row[1]) + "\",\"" + str(recorder_row[2]) + "\"," + str(quality_row[0]) + ",\"" + quality_row[1] + "\"\n")
        cursor.close ()
        con.close ()
        f.close()
        return export_dir


def exportsite(siteID, export_dir):
	export_dir = "site" +  "_" + siteID + "_" + export_dir
	os.mkdir(export_dir)
        try:
                con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
        except MySQLdb.Error, e:
                print "Error %d: %s" % (e.args[0], e.args[1])
                sys.exit (1)
        cursor = con.cursor()
        query = "SELECT SoundID, ColID, DirID, OriginalFilename FROM Sounds \
			WHERE SiteID='" + siteID + "' AND SoundStatus!='9'"
        cursor.execute (query)
        file_rows = cursor.fetchall ()
        f = open(export_dir + "/data.csv", 'a')
	f.write("SoundID,Filename,Date,Time,SamplingRate,BitRate,Channels,Duration,FileFormat,")
	f.write("SiteID,SiteName,Latitude,Longitude,SoundNotes,ColID,CollectionName,")
	f.write("CollectionNotes,Sensor_Recorder,Sensor_Microphone,Sensor_Notes,QualityFlagID,QualityFlag\n")
	for file_row in file_rows:
		SoundID = str(file_row[0])
		filename = file_row[3]
	        thisfile = server_dir + "sounds/sounds/" + str(file_row[1]) + "/" + str(file_row[2]) + "/" + filename
        	shutil.copyfile(thisfile, export_dir + "/" + filename)
        	
        	
        	#get sound info
        	query1 = "SELECT Sounds.OriginalFilename, Sounds.Date, TIME_FORMAT(Sounds.Time, '%H:%i:%S'), \
        			Sounds.SamplingRate, Sounds.BitRate, Sounds.Channels, \
				Sounds.Duration, Sounds.SoundFormat, Sounds.SiteID, \
				Sites.SiteName, Sites.SiteLat, Sites.SiteLon, Sounds.Notes, \
				Collections.ColID, Collections.CollectionName, Collections.Notes \
				FROM Sounds, Sites, Collections \
				WHERE Sounds.ColID=Collections.ColID AND \
				Sounds.SiteID=Sites.SiteID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1"
		cursor.execute (query1)
		if cursor.rowcount==1:
			sound_row = cursor.fetchone ()
	
		#if isinstance(sound_row[12], types.NoneType):
		
        	#get recorder info
        	query1 = "SELECT Sensors.Recorder, Sensors.Microphone, Sensors.Notes \
				FROM Sounds, Sensors \
				WHERE Sounds.SensorID = Sensors.SensorID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1"
		cursor.execute (query1)
		if cursor.rowcount==1:
			recorder_row = cursor.fetchone ()
		else:
			recorder_row = ["None", "None", "None"]

        	#get qualflags
        	query2 = "SELECT QualityFlags.QualityFlagID, QualityFlags.QualityFlag \
				FROM Sounds, QualityFlags \
				WHERE Sounds.QualityFlagID=QualityFlags.QualityFlagID AND \
				Sounds.SoundID='" + SoundID + "' LIMIT 1";
		cursor.execute (query2)
		if cursor.rowcount==1:
			qual=True;
			quality_row = cursor.fetchone ()
    	        f.write(SoundID + ",\"" + sound_row[0] + "\"," + str(sound_row[1]) + "," + str(sound_row[2]) + "," + str(sound_row[3]) + "," + str(sound_row[4]) + "," + str(sound_row[5]) + "," + str(sound_row[6]) + "," + sound_row[7] + "," + str(sound_row[8]) + ",\"" + sound_row[9] + "\"," + str(sound_row[10]) + "," + str(sound_row[11]) + "," + str(sound_row[12]) + ",\"" + str(sound_row[13]) + "\"," + sound_row[14] + ",\"" + str(sound_row[15]) + "\",\"" + str(recorder_row[0]) + "\",\"" + str(recorder_row[1]) + "\",\"" + str(recorder_row[2]) + "\"," + str(quality_row[0]) + ",\"" + quality_row[1] + "\"\n")
        cursor.close ()
        con.close ()
        f.close()
        return export_dir
        
#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

confirmid="n"

while confirmid=="n":
	export_type = raw_input('\n\nSelect how to select files to export: [c]ollections or [s]ites: ')
	if export_type=='c' or export_type=='s': 
		confirmid="y"
		continue
	else:
		print "Error, please type the letter \"c\" for collection or \"s\" for sites\n"
		confirmid="n"
		continue

confirmid="n"
try:
	if export_type=='c':
		while confirmid=="n":
			getcollections()
			ColID = raw_input('\n\nEnter the ID of the collection to save the files into: ')
			confirmcollection(ColID)
			while confirmid!="y":
				confirmid = raw_input('\nIs this the correct collection? [y/n]: ')
				if confirmid == "y" or confirmid == "n":
					if confirmid=='y': continue
					if confirmid=='n': break
				else:
					print "Error, please type the letter \"y\" for yes or \"n\" for no\n"

		with Ticker("\n Exporting data..."):
			export_dir = exportcollection(str(ColID), export_dir)



	elif export_type=='s': 
		while confirmid=="n":
			getsites()
			SiteID = raw_input('\n\nEnter the ID of the site to save the files into: ')
			confirmsite(SiteID)
			while confirmid!="y":
				confirmid = raw_input('\nIs this the correct site? [y/n]: ')
				if confirmid == "y" or confirmid == "n":
					if confirmid=='y': continue
					if confirmid=='n': break
				else:
					print "Error, please type the letter \"y\" for yes or \"n\" for no\n"
		with Ticker("\n Exporting data..."):
			export_dir = exportsite(str(SiteID), export_dir)

	with Ticker("\n Creating archive file..."):
		cleanup(export_dir, export_format)


except (KeyboardInterrupt):
	print "\n\n Interrupt command received...\n  exiting..."
	when_stop=datetime.datetime.now().strftime("  Script keyboard-interrupted on %d/%b/%y %H:%M\n")
	print when_stop

	status, output = commands.getstatusoutput('rm *.pyc')
	sys.exit (0) #Exit normally

	
status, output = commands.getstatusoutput('rm *.pyc')

process_date = datetime.datetime.now().strftime("\n\n File export complete.\n")

print process_date
sys.exit (0)
