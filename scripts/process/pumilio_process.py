#!/usr/bin/python
#For Pumilio 2.1.0 or more recent

"""
v. 2.1.0a (22 Oct 2011)
Main script to trigger R or Python scripts stored in a MySQL database.
This script is to be used as part of Pumilio in the script jobs.
"""

#########################################################################
# HEADER DECLARATIONS							#
#########################################################################
 
# Import modules
import commands
import os
import sys
import datetime
import time
import socket
import ConfigParser

try:
	import MySQLdb
except:
	print "\nMySQLdb is not installed. To install in Ubuntu use: \n  sudo apt-get install python-mysqldb\n"
	sys.exit (1)

#########################################################################
# GLOBAL VARIABLES							#
#########################################################################

this_computer_ip=socket.gethostname()
try:
	from configfile import *
except:
	print "\n The configuration file is missing.\n  Rename the file configfile.py.dist to configfile.py\n  and fill the values.\n"
	sys.exit (1)

if use_section==2:
	status, output = commands.getstatusoutput('wget ' + web_config)
	if status != 0:
		print output
		print "\n ERROR: Could not find the file " + web_config + "\n please check your configuration and try again.\n\n"
		sys.exit (1) #Exit with error
	#from web_config import *
	##From http://stackoverflow.com/questions/924700/best-way-to-retrieve-variable-values-from-a-text-file-python-json
	config = ConfigParser.ConfigParser()

	config.read("web_config.txt")
	db_hostname = config.get("myvars", "db_hostname")
	db_database = config.get("myvars", "db_database")
	db_username = config.get("myvars", "db_username")
	db_password = config.get("myvars", "db_password")
	web_location = config.get("myvars", "web_location")

#########################################################################
# SOFTWARE CHECK							#
#########################################################################

status, output = commands.getstatusoutput('sox --version')
if status != 0:
	print output
	print "\n ERROR: SoX is not installed.\n\n"
	sys.exit (1) #Exit with error

#########################################################################
# FUNCTION DECLARATIONS							#
#########################################################################
 

#Extract wav from FLAC
def getwav(ProcessID, filelocation, filename, fileformat):

	status, output = commands.getstatusoutput('wget ' + filelocation)
	if status != 0:
		print output
		logdb(ProcessID, this_computer_ip, "Could not download file: " + filename + ". " + output)
		updatefile(ProcessID, 5, this_computer_ip)
		return 1

	if fileformat=="flac":
		status2, output2 = commands.getstatusoutput('flac --version')
		if status2 != 0:
			print output2
			print "\n ERROR: FLAC is not installed.\n\n"
			sys.exit (1) #Exit with error


		status, output = commands.getstatusoutput('flac -d ' + filename + ' -o 1.wav')
		status, output = commands.getstatusoutput('rm -f ' + filename)
	elif fileformat=="wav":
		status, output = commands.getstatusoutput('mv ' + filename + ' 1.wav')
	else:
		status, output = commands.getstatusoutput('sox ' + filename + ' 1.wav')
		status, output = commands.getstatusoutput('rm -f ' + filename)

	status, output = commands.getstatusoutput('rm ' + filename)
	return '1.wav'


#Get the script from MySQL
def getscript(scriptID):
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "\n\n Database Error %d: %s" % (e.args[0], e.args[1])
		print "\n Could not connect to the database! leaving the program..."
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT Language, Script FROM Scripts WHERE ScriptID='" + str(scriptID) + "' LIMIT 1";
	cursor.execute (query)
	row = cursor.fetchone ()
	cursor.close ()
	con.close ()
	return row




#Get the number of tasks completed by this computer
def get_no_tasks(ip):
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "\n\n Database Error %d: %s" % (e.args[0], e.args[1])
		print "\n Could not connect to the database! leaving the program..."
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT COUNT(*) AS Done FROM Queue WHERE ComputerDone='" + ip + "' AND Status='2'";
	cursor.execute (query)
	row = cursor.fetchone ()
	cursor.close ()
	con.close ()
	return row


#Get a file to process from MySQL
def getfile():
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Database Error %d: %s" % (e.args[0], e.args[1])
		print "\n Could not connect to the database! leaving the program..."
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT Sounds.SoundID, Queue.QueueID, Sounds.ColID, Sounds.DirID, Sounds.OriginalFilename, Queue.ScriptID, Sounds.SoundFormat FROM Queue,Sounds WHERE Queue.Status='0' AND Queue.SoundID=Sounds.SoundID ORDER BY Priority, QueueID LIMIT 1";
	cursor.execute (query)
	row = cursor.fetchone ()
	cursor.close ()
	con.close ()
	return row


#Update status in MySQL
def updatefile(ProcessID, statusID, computer_ip):
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Database Error %d: %s" % (e.args[0], e.args[1])
		print "\n Could not connect to the database! leaving the program..."
		sys.exit (1)
	cursor = con.cursor()
	con.autocommit(True)
	if statusID==0:
		query = "UPDATE Queue SET Status='" + str(statusID) + "' WHERE QueueID='" + str(ProcessID) + "' LIMIT 1"
		cursor.execute (query)
	elif statusID==1:
		query = "UPDATE Queue SET Status='" + str(statusID) + "', ClaimedDate=NOW(), ComputerDone='" + computer_ip + "' WHERE QueueID='" + str(ProcessID) + "' LIMIT 1"
		cursor.execute (query)
		query2 = "DELETE FROM ProcessLog WHERE QueueID='" + str(ProcessID) + "'"
		cursor.execute (query2)
	elif statusID==2:
		query = "UPDATE Queue SET Status='" + str(statusID) + "', ProcessDoneDate=NOW(), ComputerDone='" + computer_ip + "' WHERE QueueID='" + str(ProcessID) + "' LIMIT 1"
		cursor.execute (query)
	elif statusID==3:
		query = "UPDATE Queue SET Status='" + str(statusID) + "' WHERE QueueID='" + str(ProcessID) + "' LIMIT 1"
		cursor.execute (query)
	elif statusID==5:
		query = "UPDATE Queue SET Status='" + str(statusID) + "' WHERE QueueID='" + str(ProcessID) + "' LIMIT 1"
		cursor.execute (query)
	return


#Write log to MySQL
def logdb(ProcessID, computer_ip, log):
	log = log.replace('"', '\"').replace("'", "\'")
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Database Error %d: %s" % (e.args[0], e.args[1])
		print "\n Could not connect to the database! leaving the program..."
		sys.exit (1)
	cursor = con.cursor()
	con.autocommit(True)
	query = "INSERT INTO ProcessLog (`QueueID` ,`Computer` ,`TimeStamp` ,`FileLog`) VALUES ('" + str(ProcessID) + "', '" + computer_ip + "', NOW() , %s)"
	cursor.execute (query, (log,))
	return

 
#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################


#Clean up directory, just in case
status, output = commands.getstatusoutput('rm script.*')

status=''
output=''

while True:

	no_tasks = get_no_tasks(this_computer_ip)
	no_tasks = int(no_tasks[0])
	print " ====================================================="
	print " This computer has completed " + str(no_tasks) + " tasks."
	print " ====================================================="
	#The following is to mantain a way to allow the user to cancel the program
	# while exiting in a clean manner
	# from http://effbot.org/zone/stupid-exceptions-keyboardinterrupt.htm
	try:

		#Cleanup directory
		status1, output1 = commands.getstatusoutput('rm *.wav')
		status1, output1 = commands.getstatusoutput('rm *.flac')
		status1, output1 = commands.getstatusoutput('rm script.*')

		#Get the file and the path from MySQL
		print " Getting a file to process from the server...\n"
		row = getfile()
		if row==None:
			print " No new files are waiting in the queue...\n  waiting for a minute..."
			print " To stop, press Ctrl-c\n"
			time.sleep(60) #Sleep for 1 minute, then try again.
			if use_section==2:
				status1, output1 = commands.getstatusoutput('rm web_config.txt')
				status, output = commands.getstatusoutput('wget ' + web_config)
				if status != 0:
					print output
					print "\n ERROR: Could not find the file " + web_config + "\n please check your configuration and try again.\n\n"
					sys.exit (1) #Exit with error
				
				#from web_config import *
				config = ConfigParser.ConfigParser()

				config.read("web_config.txt")
				db_hostname = config.get("myvars", "db_hostname")
				db_database = config.get("myvars", "db_database")
				db_username = config.get("myvars", "db_username")
				db_password = config.get("myvars", "db_password")
				web_location = config.get("myvars", "web_location")


		elif len(row)==7:
			SoundID, ProcessID, ColID, DirID, filename, scriptID, fileformat= row
			SoundID = str(SoundID)
			ProcessID = str(ProcessID)
			scriptID = str(scriptID)
			ColID = str(ColID)
			DirID = str(DirID)

			file_to_process='http://' + web_location + 'sounds/sounds/' + ColID + '/' + DirID + '/' + filename
			print " Processing file " + file_to_process

			#update the record on MySQL as taken
			updatefile(ProcessID, 1, this_computer_ip)

			#extract wav from flac file
			print " Extracting file from server...\n"
			wav_file=getwav(ProcessID, file_to_process, filename, fileformat)

			#Check that the wav file was extracted from the flac
			if wav_file==1:
				print "There was a problem with file " + filename
				print " Cleaning up...\n "
				#updatefile(ProcessID, 3, this_computer_ip)
				#logdb(ProcessID, this_computer_ip, "Could not obtain the wav file.")
				del(ProcessID)
			else:
				#get the script
				language, script = getscript(scriptID)

				if language=="R":
					print " Executing R script...\n\n"
					print " To stop, press Ctrl-c\n"
					#save the script obtained from the database in a file
					scriptfile = open ( 'script.R', 'w' )
					scriptfile.write (script)
					scriptfile.close()
					del(script)
					#execute R script on file
					status, output = commands.getstatusoutput('Rscript --vanilla script.R ' + wav_file + ' ' + str(SoundID) + ' ' + str(ProcessID))
					if status != 0:
						print "There was a problem with the script and could not complete."
						print " "
						print output
						print " Cleaning up...\n "
						commands.getstatusoutput('rm *.wav')
						os.remove('script.R')
						updatefile(ProcessID, 3, this_computer_ip)
						logdb(ProcessID, this_computer_ip, "Problem with file or script: " + output)
						del(ProcessID)
					else:
						logdb(ProcessID, this_computer_ip, datetime.datetime.now().strftime("Script completed on %d/%b/%y %H:%M\n") + output)
						commands.getstatusoutput('rm *.wav')
						os.remove('script.R')
						updatefile(ProcessID, 2, this_computer_ip)
						del(ProcessID)
						print "  Script completed.\n"


				elif language=="Python":
					print "\n\n Executing Python script..."
					print "\n\n To stop, press Ctrl-c"
					#save the script obtained from the database in a file
					scriptfile = open ( 'script.py', 'w' )
					scriptfile.write (script)
					scriptfile.close()
					del(script)
					#execute R script on file
					status, output = commands.getstatusoutput('python script.py ' + wav_file + ' ' + str(SoundID) + ' ' + str(ProcessID))
					if status != 0:
						print "There was a problem with file " + file_to_process
						print " or with the script " + scriptID
						print " "
						print output
						print "\n  Cleaning up..."
						status1, output1 = commands.getstatusoutput('rm *.wav')
						os.remove('script.py')
						updatefile(ProcessID, 3, this_computer_ip)
						logdb(ProcessID, this_computer_ip, "Problem with file or script: " + output)
						del(ProcessID) 
					else:
						logdb(ProcessID, this_computer_ip, datetime.datetime.now().strftime("Script completed on %d/%b/%y %H:%M\n") + output)
						status1, output1 = commands.getstatusoutput('rm *.wav')
						os.remove('script.py')
						updatefile(ProcessID, 2, this_computer_ip)
						del(ProcessID)
						print "  Script completed.\n"



	except (KeyboardInterrupt):
		print "\n\n Interrupt command received...\n  cleaning up, please wait..."

		#Clean up directory
		status1, output1 = commands.getstatusoutput('rm *.wav')
		status1, output1 = commands.getstatusoutput('rm *.flac')
		status1, output1 = commands.getstatusoutput('rm script.*')
		if use_section==2:
			status1, output1 = commands.getstatusoutput('rm web_config.txt')

		#If ProcessID exists, use it to update the database, otherwise use 0
		try:
			ProcessID
		except NameError:
			logdb(0, this_computer_ip, datetime.datetime.now().strftime("Script keyboard-interrupted on %d/%b/%y %H:%M\n") + output)
		else:
			updatefile(ProcessID, 0, this_computer_ip)
			logdb(ProcessID, this_computer_ip, datetime.datetime.now().strftime("Script keyboard-interrupted on %d/%b/%y %H:%M\n") + output)
		print " To restart analysis, type:  ./pumilio_process.py\n  and press [ENTER]\n\n"
		sys.exit (0) #Exit normally
	except (SystemExit):
		print "\n\n Ending program...\n  cleaning up, please wait..."

		#Clean up directory
		status1, output1 = commands.getstatusoutput('rm *.wav')
		status1, output1 = commands.getstatusoutput('rm *.flac')
		status1, output1 = commands.getstatusoutput('rm script.*')
		if use_section==2:
			status1, output1 = commands.getstatusoutput('rm web_config.txt')

		#If ProcessID exists, use it to update the database, otherwise use 0
		try:
			ProcessID
		except NameError:
			logdb(0, this_computer_ip, datetime.datetime.now().strftime("Script ended on %d/%b/%y %H:%M\n") + output)
		else:
			logdb(ProcessID, this_computer_ip, datetime.datetime.now().strftime("Script ended on %d/%b/%y %H:%M\n") + output)
			updatefile(ProcessID, 0, this_computer_ip)

		print " To restart analysis, type:  ./pumilio_process.py\n  and press [ENTER]\n\n"
		sys.exit (0) #Exit normally
	except Exception as inst:
		print "\n\n Error, cleaning up, please wait..."

		#Clean up directory
		status1, output1 = commands.getstatusoutput('rm *.wav')
		status1, output1 = commands.getstatusoutput('rm *.flac')
		status1, output1 = commands.getstatusoutput('rm script.*')
		if use_section==2:
			status1, output1 = commands.getstatusoutput('rm web_config.txt')
		
		print type(inst)
		print inst.args
		print inst
		#If ProcessID exists, use it to update the database, otherwise use 0
		try:
			ProcessID
		except NameError:
			logdb(0, this_computer_ip, datetime.datetime.now().strftime("Script ended unexpectedly on %d/%b/%y %H:%M\n") + output)
		else:
			logdb(ProcessID, this_computer_ip, datetime.datetime.now().strftime("Script ended unexpectedly on %d/%b/%y %H:%M\n") + output)
			updatefile(ProcessID, 3, this_computer_ip)


