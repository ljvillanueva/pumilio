#!/usr/bin/python
# For Pumilio 2.4.0 or recent
# Automatic script to add files to the database

#########################################################################
# HEADER DECLARATIONS							#
#########################################################################

# Import modules
import commands
import os
import wave
import sys
import hashlib
import MySQLdb
import subprocess
import linecache

#########################################################################
# COMMAND LINE ARGUMENTS						#
#########################################################################

# Place "global" variables in the namespace
logfile = "log.txt"

db_hostname=linecache.getline('configfile.php', 2)
db_database=linecache.getline('configfile.php', 3)
db_username=linecache.getline('configfile.php', 4)
db_password=linecache.getline('configfile.php', 5)
server_dir=linecache.getline('configfile.php', 6)

db_hostname=db_hostname.replace("\n", "")
db_database=db_database.replace("\n", "")
db_username=db_username.replace("\n", "")
db_password=db_password.replace("\n", "")
server_dir=server_dir.replace("\n", "")

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
	
def cleanup(server_dir, ColID, DirID, FullPath, OriginalFilename, ToAddMemberID):
	pathToSound = server_dir + 'sounds/sounds/' + ColID + '/' + DirID
	if os.path.exists(server_dir)==0:
		f = open(logfile, 'a')
		f.write("\n \n Could not find the sounds/ directory. Check your settings and try again.\n")
		f.close()
		updatefile(ToAddMemberID, str(9), "Could not find the sounds/ directory. Check your settings and try again.")
		sys.exit(2)
	#	
	if os.path.exists(server_dir + 'sounds/sounds/' + ColID)==0:
		status, output = commands.getstatusoutput('mkdir ' + server_dir + 'sounds/sounds/' + ColID)
		if status != 0:
			f = open(logfile, 'a')
			f.write(" ERROR: Could not create necessary folder " + server_dir + 'sounds/sounds/' + ColID)
			f.close()
			updatefile(ToAddMemberID, str(9), "ERROR: Could not create necessary folder " + server_dir + 'sounds/sounds/' + ColID)
                       	sys.exit(3)
	if os.path.exists(pathToSound)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToSound)
		if status != 0:
			f = open(logfile, 'a')
			f.write(" ERROR: Could not create necessary folder " + pathToSound)
			f.close()
			updatefile(ToAddMemberID, str(9), "ERROR: Could not create necessary folder " + pathToSound)
                       	sys.exit(4)
	#
	#Move the already processed file to a done folder
	status, output = commands.getstatusoutput('cp ' + FullPath + ' ' + pathToSound + '/')
	if status != 0:
		f = open(logfile, 'a')
		f.write(" ERROR: Could not copy the file " + FullPath + " to the server directory")
		f.close()
		updatefile(ToAddMemberID, str(9), "ERROR: Could not copy the file to the server directory")
		sys.exit(5)
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
		updatefile(ToAddMemberID, str(9), "Weird file, could not determine bits")
		sys.exit(6)
	wave_vars['max_time'] = wave_vars['num_samps'] / wave_vars['samp_rate']
	# Print wave file values, mostly to debug
	#print "Wave values: "
	#for item in wave_vars.iteritems():
	#	print item
	return wave_vars

#Insert data to MySQL
def tomysql(item_wav, OriginalFilename, FullPath, FileFormat, file_md5, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID):
	filesize=os.path.getsize(FullPath)
	wave_pointer = open_wave(item_wav)
	wave_vars = find_values(wave_pointer)
	SoundID=insert(OriginalFilename, FileFormat, wave_vars['no_channels'], wave_vars['samp_rate'], wave_vars['bits'], wave_vars['max_time'], file_md5, str(filesize), ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID)
	updatefile(ToAddMemberID, str(0))
	#print "\n  MySQL Insert was successful"
	return SoundID

def insert(soundname, soundformat, no_channels, samplingrate, bitres, soundlength, file_md5, filesize, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (7)
	cursor = con.cursor()
	sounddate = Date[0:4] + '-' + Date[4:6] + '-' + Date[6:8]
	if len(Time)==5:
		Time = "0" + Time
	soundtime = Time[0:2] + ':' + Time[2:4] + ':' + Time[4:6]
	query = "INSERT INTO Sounds (ColID, SoundName, OriginalFilename, SoundFormat, Channels, SamplingRate, BitRate, Duration, Date, Time, MD5_hash, FileSize, SiteID, DirID, SensorID) \
         VALUES (" + \
	`ColID` + ', ' + `soundname` + ', ' + `soundname` + ', ' + `soundformat` + ', ' + `no_channels` + ', ' + `samplingrate` + ', ' + `bitres` + ', ' + `soundlength` + ', ' + `sounddate` + ', ' + `soundtime` + ', ' + `file_md5` +  ', ' + `filesize` + ', ' + `SiteID` + ', ' + `DirID` + ', ' + `SensorID` + ')'
	#print "Query: " + query + "\n"
	cursor.execute (query)
	SoundID=con.insert_id()
	#Close MySQL
	cursor.close ()
	con.close ()
	return str(SoundID)

def updatefile(ToAddMemberID, Status, message=""):
	Status = str(Status)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (8)
	cursor = con.cursor()
	con.autocommit(True)
	query = "UPDATE FilesToAddMembers SET ReturnCode=" + `Status` + ", ErrorCode=" + `message` + " WHERE ToAddMemberID=" + `ToAddMemberID` + " LIMIT 1"
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
	query = "SELECT ToAddMemberID, FullPath, OriginalFilename, DATE_FORMAT(Date, '%Y%m%d') AS Date, DATE_FORMAT(Time, '%H%i%s') AS Time, SiteID, ColID, DirID, SensorID FROM FilesToAddMembers WHERE ReturnCode='1'"
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

def checkfile(soundname):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT * FROM Sounds WHERE SoundName='" + soundname + "'"
	cursor.execute (query)
	how_many = cursor.rowcount
	cursor.close ()
	con.close ()
	return str(how_many)

#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

#Get all soundfiles
results=getallsounds()

try:
	for row in results:
		ToAddMemberID = row[0]
		ToAddMemberID = str(int(ToAddMemberID))
		FullPath = row[1]
		OriginalFilename = row[2]
		Date = row[3]
		Date = str(int(Date))
		Time = row[4]
		Time = str(int(Time))
		SiteID = row[5]
		SiteID = str(int(SiteID))
		ColID = row[6]
		ColID = str(int(ColID))
		DirID = row[7]
		DirID = str(int(DirID))
		SensorID = row[8]
		SensorID = str(int(SensorID))

		file_check = checkfile(OriginalFilename)
		if file_check == 1:
			updatefile(ToAddMemberID, str(9), "File already exists in archive")
			continue

		if fileExists(FullPath)==0:
			updatefile(ToAddMemberID, str(9), "Could not find file")
			continue

		updatefile(ToAddMemberID, str(2))

		status, output = commands.getstatusoutput('cp ' + FullPath + ' ' + server_dir + 'tmp/' + OriginalFilename)
		FullPath = server_dir + 'tmp/' + OriginalFilename
			
		p = subprocess.Popen(['./soundcheck.py', FullPath],stdout=subprocess.PIPE,stderr=subprocess.PIPE)
		output, errors = p.communicate()
		FileFormat = output[:-1]
		
		if FileFormat != 'wav':
			item_wav = extractflac(FullPath, FileFormat)
		else:
			item_wav = FullPath

		if item_wav == '0' or item_wav == '1':
			continue

		file_md5=getmd5(FullPath)

		SoundID=tomysql(item_wav, OriginalFilename, FullPath, FileFormat, file_md5, ColID, SiteID, DirID, SensorID, Date, Time, ToAddMemberID)
		cleanup(server_dir, ColID, DirID, FullPath, OriginalFilename, ToAddMemberID)

		status, output = commands.getstatusoutput('rm ' + FullPath)


except: #Don't know what happened
	commands.getstatusoutput('rm *.pyc')
	sys.exit (10) #Exit with error
	
commands.getstatusoutput('rm *.pyc')
sys.exit (0)