#!/usr/bin/python
# For Pumilio 2.4.0 or recent
# Automatic script to launch R to get extra stats of files

#########################################################################
# HEADER DECLARATIONS							#
#########################################################################

# Import modules
import commands
import os
import sys
import MySQLdb
import subprocess
import linecache

#########################################################################
# VARIABLES								#
#########################################################################

try:
	from configfile import *
except:
	print "\n The configuration file is missing.\n  Rename the file configfile.py.dist to configfile.py\n  and fill the values.\n"
	sys.exit (1)

#########################################################################
# FUNCTION DECLARATIONS							#
#########################################################################

#Extract wav from FLAC
def extractflac(item_flac, FileFormat):
	if FileFormat == 'flac':
		item_wav = item_flac[:-5] + '.wav'
		status, output = commands.getstatusoutput('flac -dFf ' + item_flac + ' -o ' + item_wav)
		if status != 0:
			print " "
			print "There was a problem processing " + item_flac + "!"
			print output
			updatefile(ToAddMemberID, str(9), "There was a problem processing the file with the flac decoder")
			item_wav = 2
	else:
		FileFormat_len = len(FileFormat)
		item_wav = item_flac[:-FileFormat_len] + 'wav'
		status, output = commands.getstatusoutput('sox ' + item_flac + ' ' + item_wav)
		if status != 0:
			updatefile(ToAddMemberID, str(9), "There was a problem converting the file to wav using SoX")
			item_wav = 0
	return item_wav


def fileExists(f):
	try:
		file = open(f)
	except IOError:
		exists = 0
	else:
		exists = 1
	return exists
	

def updatefile(SoundID, Status):
	Status = str(Status)
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (8)
	cursor = con.cursor()
	con.autocommit(True)
	query = "UPDATE Sounds SET SoundStats=" + `Status` + " WHERE SoundID=" + `SoundID` + " LIMIT 1"
	cursor.execute (query)
	#Close MySQL
	cursor.close ()
	con.close ()
	return


def getallsounds():
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT OriginalFilename, ColID, DirID, SoundFormat, SoundID FROM Sounds WHERE SoundStats='0' AND SoundStatus!='9'"
	cursor.execute (query)
	if cursor.rowcount == 0:
		cursor.close ()
		con.close ()
		sys.exit(0)
	else:
		results = cursor.fetchall()
	cursor.close ()
	con.close ()
	return results


#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

#Get all soundfiles
results=getallsounds()

try:
	for row in results:
		OriginalFilename = row[0]
		ColID = row[1]
		ColID = str(int(ColID))
		DirID = row[2]
		DirID = str(int(DirID))
		SoundFormat = row[3]
		SoundID = str(row[4])

		file_check = fileExists(server_dir + '/sounds/sounds/' + ColID + '/' + DirID + '/' + OriginalFilename)
		if file_check != 1:
			updatefile(SoundID, str(9))
			continue


		updatefile(SoundID, str(1))

		status, output = commands.getstatusoutput('cp ' + server_dir + '/sounds/sounds/' + ColID + '/' + DirID + '/' + OriginalFilename + ' .')
		FileName = OriginalFilename

		if SoundFormat != 'wav':
			item_wav = extractflac(OriginalFilename, SoundFormat)
			FileName = item_wav


		p = subprocess.Popen(['Rscript', '--vanilla', 'getstats.R', FileName, SoundID, db_hostname, db_database, db_username, db_password, db_value, max_freq, freq_step, segment_length], stdout=subprocess.PIPE,stderr=subprocess.PIPE)

		output, errors = p.communicate()
		exitcode = str(output)
		
		if exitcode == "0":
			updatefile(SoundID, str(2))
		else:
			updatefile(SoundID, str(9))
			continue


except: #Don't know what happened
	commands.getstatusoutput('rm *.pyc')
	os.remove(OriginalFilename)
	os.remove(FileName)
	sys.exit (0) #Exit

	
commands.getstatusoutput('rm *.pyc')
sys.exit (0)
