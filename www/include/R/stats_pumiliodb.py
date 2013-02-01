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
# COMMAND LINE ARGUMENTS						#
#########################################################################

db_hostname=linecache.getline('configfile.php', 2)
db_database=linecache.getline('configfile.php', 3)
db_username=linecache.getline('configfile.php', 4)
db_password=linecache.getline('configfile.php', 5)
server_dir=linecache.getline('configfile.php', 6)
random_val=linecache.getline('configfile.php', 7)
db_value=linecache.getline('configfile.php', 8)
max_freq=linecache.getline('configfile.php', 9)
freq_step=linecache.getline('configfile.php', 10)
segment_length=linecache.getline('configfile.php', 11)

db_hostname=db_hostname.replace("\n", "")
db_database=db_database.replace("\n", "")
db_username=db_username.replace("\n", "")
db_password=db_password.replace("\n", "")
server_dir=server_dir.replace("\n", "")
random_val=str(random_val.replace("\n", ""))

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

		file_check = fileExists('../../sounds/sounds/' + ColID + '/' + DirID + '/' + OriginalFilename)
		if file_check != 1:
			updatefile(SoundID, str(9))
			continue


		updatefile(SoundID, str(1))

		status, output = commands.getstatusoutput('cp ../../sounds/sounds/' + ColID + '/' + DirID + '/' + OriginalFilename + ' .')
		FullPath = OriginalFilename

		if SoundFormat != 'wav':
			item_wav = extractflac(OriginalFilename, SoundFormat)
			FullPath = item_wav


		p = subprocess.Popen(['Rscript', '--vanilla', 'getstats.R', FullPath, SoundID, db_hostname, db_database, db_username, db_password, db_value, max_freq, freq_step, segment_length], stdout=subprocess.PIPE,stderr=subprocess.PIPE)

		output, errors = p.communicate()
		exitcode = str(output)
		
		if exitcode == "0":
			updatefile(SoundID, str(2))
		else:
			updatefile(SoundID, str(9))
			continue


except: #Don't know what happened
	commands.getstatusoutput('rm *.pyc')
	sys.exit (9) #Exit with error

	
commands.getstatusoutput('rm *.pyc')
sys.exit (0)
