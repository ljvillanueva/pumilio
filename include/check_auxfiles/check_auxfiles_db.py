#!/usr/bin/python
#For Pumilio 2.2.1 and recent
#This version is to be called by the server when showing results. 
#

"""
v. 2.2.1a (7 Jun 2012)
Script to create mp3 and png files of sound files in flac format for Pumilio from the 
 records in the database.
 Edit the file configfile.py.dist with the appropiate values and save it as configfile.py

The script is made to create the auxiliary files for Pumilio and assumes that the original
 files are in the database already.

The script assumes it can write on the sounds/ directory of Pumilio. It should be a local folder
 or be mounted locally, for example using CIFS, SSHFS, NFS, etc and this computer must be able
 to write on it.

The script will only work on flac files.

Whenever there is an error, the script will try to give information on what the problem was, 
 and write a log with all the errors.
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
import types
import MySQLdb

# Place "global" variables in the namespace
try:
	from configfile import *
except:
	print "\n The configuration file is missing.\n  Rename the file configfile.py.dist to configfile.py\n  and fill the values.\n"
	sys.exit (1)

#########################################################################
# Get SoundID
#########################################################################

SoundID = sys.argv[1]

#########################################################################
# FUNCTION DECLARATIONS							#
#########################################################################

#Extract wav from FLAC
def extractflac(item, item_flac):
	item_wav = item_flac[:-5] + '.wav'
	status, output = commands.getstatusoutput('flac -dFf ' + item + ' -o ' + cur_dir + '/' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	return item_wav
	
#Extract wav from another using sox
def extractsox(item, item_flac, item_format):
	suffix_len = len(item_format)
	suffix_len1 = suffix_len + 1
	item_wav = item_flac[:-suffix_len1] + '.wav'
	status, output = commands.getstatusoutput('sox ' + item + ' ' + cur_dir + '/' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	return item_wav

#Draw Waveform and Spectrogram to png files
def draw_png(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette):
	#Nyquist freq
	nyquist=str(int(sampling_rate)/2)
	
	if max_freq_draw == 0:
		max_freq_draw = nyquist
		max_freq_draw_t = nyquist + " Hz"
	else:
		max_freq_draw = str(max_freq_draw)
		max_freq_draw_t = max_freq_draw + " Hz"

	half_max_freq_draw = str(int(max_freq_draw) / 2)
	half_max_freq_draw_t = half_max_freq_draw + " Hz"

	if spectrogram_palette == 1:
		letter_color = "white"
	elif spectrogram_palette == 2:
		letter_color="black"

	#Medium size
	status, output = commands.getstatusoutput('./svt.py -o 1 -a ' + item_flac[:-5] + '_w1.png -s ' + item_flac[:-5] + '_s1.png -w 600 -h 300 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" -draw \"text 5,155 '" + half_max_freq_draw_t + "'\" " + item_flac[:-5] + "_s1.png -quality 10 " + str(SoundID) + "_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "_s1.png")
	text1 = "convert " + item_flac[:-5] + "_w1.png -quality 10 " + str(SoundID) + "_w.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "_s1.png")
	commands.getstatusoutput("rm " + item_flac[:-5] + "_w1.png")
			
		
	#Small size
	status, output = commands.getstatusoutput('./svt.py -o 1 -a ' + item_flac[:-5] + '-small_w1.png -s ' + item_flac[:-5] + '-small_s1.png -w 300 -h 150 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" " + item_flac[:-5] + "-small_s1.png -quality 10 " + str(SoundID) + "-small_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "-small_s1.png")
	text1 = "convert " + item_flac[:-5] + "-small_w1.png -quality 10 " + str(SoundID) + "-small_w.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "-small_w1.png")


	#Large size
	status, output = commands.getstatusoutput('./svt.py -o 1 -a ' + item_flac[:-5] + '-large_w1.png -s ' + item_flac[:-5] + '-large_s1.png -w 920 -h 460 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" -draw \"text 5,235 '" + half_max_freq_draw_t + "'\" " + item_flac[:-5] + "-large_s1.png -quality 10 " + str(SoundID) + "-large_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "-large_s1.png")
	text1 = "convert  " + item_flac[:-5] + "-large_w1.png -quality 10 " + str(SoundID) + "-large_w.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	commands.getstatusoutput("rm " + item_flac[:-5] + "-large_w1.png")

	print "\n  PNG files creation was successful"
	return
	
#Draw Waveform and Spectrogram to png files
def draw_png_stereo(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette):

	#Nyquist freq
	nyquist=str(int(sampling_rate)/2)
	
	if max_freq_draw == 0:
		max_freq_draw = nyquist
		max_freq_draw_t = nyquist + " Hz"
	else:
		max_freq_draw = str(max_freq_draw)
		max_freq_draw_t = max_freq_draw + " Hz"

	half_max_freq_draw = str(int(max_freq_draw) / 2)
	half_max_freq_draw_t = half_max_freq_draw + " Hz"

	if spectrogram_palette == 1:
		letter_color = "white"
	elif spectrogram_palette == 2:
		letter_color="black"

	#Medium size
	#left
	status, output = commands.getstatusoutput('./svt.py -c 1 -o 1 -a wl.png -s sl.png -w 600 -h 150 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#right
	status, output = commands.getstatusoutput('./svt.py -c 2 -o 1 -a wr.png -s sr.png -w 600 -h 150 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#combine
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate sl.png sr.png sall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate wl.png wr.png wall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)

	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" -draw \"text 5,165 '" + max_freq_draw_t + "'\" -draw \"text 590,15 'L'\" -draw \"text 590,165 'R'\" sall.png -quality 10 " + str(SoundID) + "_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput("convert -fill " + letter_color + " -draw \"text 590,15 'L'\" -draw \"text 590,165 'R'\" wall.png -quality 10 " + str(SoundID) + '_w.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput('rm wall.png sall.png wl.png sl.png wr.png sr.png')
		
		
		
		
		
	#Small size
	#left
	status, output = commands.getstatusoutput('./svt.py -c 1 -o 1 -a wl.png -s sl.png -w 300 -h 75 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#right
	status, output = commands.getstatusoutput('./svt.py -c 2 -o 1 -a wr.png -s sr.png -w 300 -h 75 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#combine
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate sl.png sr.png sall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate wl.png wr.png wall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" -draw \"text 5,85 '" + max_freq_draw_t + "'\" -draw \"text 290,15 'L'\" -draw \"text 290,85 'R'\" sall.png -quality 10 " + str(SoundID) + "-small_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput("convert -fill " + letter_color + " -draw \"text 290,15 'L'\" -draw \"text 290,85 'R'\" wall.png -quality 10 " + str(SoundID) + '-small_w.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput('rm wall.png sall.png wl.png sl.png wr.png sr.png')
		
		
	#Large size
	#left
	status, output = commands.getstatusoutput('./svt.py -c 1 -o 1 -a wl.png -s sl.png -w 920 -h 230 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#right
	status, output = commands.getstatusoutput('./svt.py -c 2 -o 1 -a wr.png -s sr.png -w 920 -h 230 -m ' + max_freq_draw + ' -f 4096 -p ' + str(spectrogram_palette) + ' ' + item_wav)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	#combine
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate sl.png sr.png sall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)
	status, output = commands.getstatusoutput('montage -tile 1x2 -mode Concatenate wl.png wr.png wall.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	text1 = "convert -fill " + letter_color + " -draw \"text 5,15 '" + max_freq_draw_t + "'\" -draw \"text 5,245 '" + max_freq_draw_t + "'\" -draw \"text 905,15 'L'\" -draw \"text 905,245 'R'\" sall.png -quality 10 " + str(SoundID) + "-large_s.png"
	status, output = commands.getstatusoutput(text1)
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput("convert -fill " + letter_color + " -draw \"text 905,15 'L'\" -draw \"text 905,245 'R'\" wall.png -quality 10 " + str(SoundID) + '-large_w.png')
	if status != 0:
		print " "
		print "There was a problem processing " + item_flac + "!"
		print output
		sys.exit(1)	
	status, output = commands.getstatusoutput('rm wall.png sall.png wl.png sl.png wr.png sr.png')
		
	return

#Make MP3 files using lame
def makemp3(SoundID, item_wav, item_flac):
	#Convert wav to mp3
	mp3file = str(SoundID) + '.autopreview.mp3'
	status, output = commands.getstatusoutput('lame -h -b 128 ' + item_wav + " " + mp3file)
	if status != 0:
		print "There was a problem creating the mp3 file of " + item_flac
		print output
		sys.exit(1)
	print "\n  MP3 creation was successful\n"
	return mp3file

def cleanup(server_dir, colID, DirID, item_flac, newpng, newmp3):
	pathToUse = server_dir
	pathToPNG = pathToUse + 'images/' + colID + '/' + DirID + '/'
	pathToMP3 = pathToUse + 'previewsounds/' + colID + '/' + DirID + '/'
	pathToSound = pathToUse + 'sounds/' + colID + '/' + DirID + '/'

	if os.path.exists(pathToUse + 'images/' + colID)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToUse + 'images/' + colID)
		if status != 0:
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod 777 ' + pathToUse + 'images/' + colID)
	if os.path.exists(pathToPNG)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToPNG)
		if status != 0:
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod 777 ' + pathToPNG)
	if os.path.exists(pathToUse + 'previewsounds/' + colID)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToUse + 'previewsounds/' + colID)
		if status != 0:
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod 777 ' + pathToUse + 'previewsounds/' + colID)
	if os.path.exists(pathToMP3)==0:
		status, output = commands.getstatusoutput('mkdir ' + pathToMP3)
		if status != 0:
			print output
                       	sys.exit(1)
		status, output = commands.getstatusoutput('chmod 777 ' + pathToMP3)

	#Move the aux files to their destination
	if newpng==1:
		status, output = commands.getstatusoutput('mv *.png ' + pathToPNG)
		if status != 0:
			print output
        	       	sys.exit(1)
	if newmp3==1:
		status, output = commands.getstatusoutput('mv *.mp3 ' + pathToMP3)
		if status != 0:
			print output
			sys.exit(1)
	return

def fileExists(f):
	try:
		file = open(f)
	except IOError:
		exists = 0
	else:
		exists = 1
	return exists

def getmd5(flac_file):
	"""
	Get the MD5 hash for the file.
	"""
	f1 = file(flac_file ,'rb')
	m = hashlib.md5()
	while True:
		t = f1.read(1024)
		if len(t) == 0: break
		m.update(t)
	return m.hexdigest()

def insertmp3(SoundID, mp3filename):
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "UPDATE Sounds SET AudioPreviewFilename=" + \
	`mp3filename` + " WHERE SoundID=" + SoundID
	cursor.execute (query)
	#Close MySQL
	cursor.close ()
	con.close ()
	return

def insertmd5(SoundID, filemd5):
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "UPDATE Sounds SET MD5_hash=" + \
	`filemd5` + " WHERE SoundID=" + \
	`SoundID`
	cursor.execute (query)
	#Close MySQL
	cursor.close ()
	con.close ()
	return

def insert_filesize(SoundID, FileSize):
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "UPDATE Sounds SET FileSize=" + \
	`FileSize` + " WHERE SoundID=" + \
	`SoundID`
	cursor.execute (query)
	#Close MySQL
	cursor.close ()
	con.close ()
	return

def check_filesize(SoundID):
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT FileSize FROM Sounds WHERE SoundID=" + \
	`SoundID`
	cursor.execute (query)
	if cursor.rowcount == 1:
		result = cursor.fetchone()
		result = result[0]
		if isinstance(result, (int, long, float, complex)) == False:
			result = 0
	else:
		result = 0
	cursor.close ()
	con.close ()
	return result
	
def insertpng(SoundID, soundname, spectrogram_palette, max_freq):
	#Open MySQL
	SoundID = str(SoundID)
	max_freq = str(max_freq)
	soundname_prefix=soundname[:-5]
	small_w= str(SoundID) + '-small_w.png'
	small_s= str(SoundID) + '-small_s.png'
	med_w= str(SoundID) + '_w.png'
	med_s= str(SoundID) + '_s.png'
	large_w= str(SoundID) + '-large_w.png'
	large_s= str(SoundID) + '-large_s.png'
	spectrogram_palette = str(spectrogram_palette)
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType,SpecMaxFreq) \
         VALUES (" + \
	`SoundID` + ", " + `small_s` + ", " + `spectrogram_palette` + ", 'spectrogram-small', " + `max_freq` + ")"
	cursor.execute (query)

	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType) \
         VALUES (" + \
	`SoundID` + ", " + `small_w` + ", " + `spectrogram_palette` + ", 'waveform-small')"
	cursor.execute (query)

	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType,SpecMaxFreq) \
         VALUES (" + \
	`SoundID` + ", " + `med_s` + ", " + `spectrogram_palette` + ", 'spectrogram', " + `max_freq` + ")"
	cursor.execute (query)

	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType) \
         VALUES (" + \
	`SoundID` + ", " + `med_w` + ", " + `spectrogram_palette` + ", 'waveform')"
	cursor.execute (query)

	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType,SpecMaxFreq) \
         VALUES (" + \
	`SoundID` + ", " + `large_s` + ", " + `spectrogram_palette` + ", 'spectrogram-large', " + `max_freq` + ")"
	cursor.execute (query)

	query = "INSERT INTO SoundsImages (SoundID,ImageFile,ColorPalette,ImageType) \
         VALUES (" + \
	`SoundID` + ", " + `large_w` + ", " + `spectrogram_palette` + ", 'waveform-large')"
	cursor.execute (query)

	#Close MySQL
	cursor.close ()
	con.close ()
	return

def delmysql(SoundID):
	#Open MySQL
	SoundID = str(SoundID)
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	cursor.execute("""DELETE FROM SoundsImages WHERE SoundID=%s""", (SoundID,))
	#Close MySQL
	cursor.close ()
	con.close ()
	return

def getsounds(SoundID):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT SoundID, ColID, DirID, OriginalFilename, SoundFormat FROM Sounds WHERE SoundID='" + SoundID + "' LIMIT 1"
	cursor.execute (query)
	if cursor.rowcount == 0:
		print "\nSoundID Not found\n"
		sys.exit(0)
	else:
		result = cursor.fetchone()
	cursor.close ()
	con.close ()
	return result
	
def getmax_freq(nyquist):
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT Value FROM PumilioSettings WHERE Settings='max_spec_freq'"
	cursor.execute (query)
	if cursor.rowcount == 1:
		result = cursor.fetchone()
		if result[0] == "max":
			result = nyquist
		else:
			result = int(result[0])			
	else:
		result = 0
	cursor.close ()
	con.close ()
	return result


def getspectrogram_palette():
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	query = "SELECT Value FROM PumilioSettings WHERE Settings='spectrogram_palette'"
	cursor.execute (query)
	if cursor.rowcount == 1:
		result = cursor.fetchone()
		result = int(result[0])
	else:
		result = 2
	cursor.close ()
	con.close ()
	return result

def checkimages(SoundID):
	#Open MySQL
	SoundID = str(SoundID)
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	cursor.execute("""SELECT COUNT(*) FROM SoundsImages WHERE SoundID=%s""", (SoundID,))
	result = cursor.fetchone()
	result = result[0]
	cursor.close ()
	con.close ()
	return result

def checkmd5(SoundID):
	SoundID = str(SoundID)
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	cursor.execute("""SELECT COUNT(*) FROM Sounds WHERE MD5_hash IS NULL AND SoundID=%s""", (SoundID,))
	result = cursor.fetchone()
	result = result[0]
	cursor.close ()
	con.close ()
	return result
	
def checkpngfiles(SoundID, ColID, DirID):
	SoundID = str(SoundID)
	ColID = str(ColID)
	DirID = str(DirID)
	check_val=0
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("waveform", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("spectrogram", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("waveform-small", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("spectrogram-small", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("waveform-large", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.execute("""SELECT ImageFile FROM SoundsImages WHERE ImageType=%s AND SoundID=%s""", ("spectrogram-large", SoundID,))
	if cursor.rowcount == 1:
		this_image = cursor.fetchone()
		this_image = this_image[0]
		if fileExists(server_dir + 'images/' + ColID + '/' + DirID + '/' + this_image):
			check_val=check_val+1
	#
	cursor.close ()
	con.close ()
	return check_val

def checkmp3file(SoundID, ColID, DirID):
	SoundID = str(SoundID)
	ColID = str(ColID)
	DirID = str(DirID)
	check_val=0
	#Open MySQL
	try:
		con = MySQLdb.connect(host=db_hostname, user=db_username, passwd=db_password, db=db_database)
	except MySQLdb.Error, e:
		print "Error %d: %s" % (e.args[0], e.args[1])
		sys.exit (1)
	cursor = con.cursor()
	#
	cursor.execute("""SELECT AudioPreviewFilename FROM Sounds WHERE SoundID=%s""", (SoundID,))
	this_file = cursor.fetchone()
	if type(this_file[0]) != types.NoneType:
		this_file = this_file[0]
		#
		if fileExists(server_dir + 'previewsounds/' + ColID + '/' + DirID + '/' + this_file):
			check_val=check_val+1
	#
	cursor.close ()
	con.close ()
	return check_val
	
def checkifstereo(wave_file):
	"""
	Open the wave file specified in the command line or elsewhere for processing.
	"""
	wave_pointer = wave.open(wave_file,'rb')
	#Check if stereo
	no_channels = wave_pointer.getnchannels()
	return no_channels

def stereo2mono(wave_file):
	"""
	Convert the stereo file to mono using Sox and default options
	"""
	#Check if sox is installed
	status, output = commands.getstatusoutput('sox')
	if output[-9:] == 'not found':
		print " "
		print "Sox is not installed!"
		print "Please install with: sudo apt-get install sox libsox*"
		print " "
		print "Exiting program."
		print " "
		sys.exit(1)
	#Get the stereo filename
	#print " Stereo file, will convert to mono."
	mono_name = "mono.wav"
	status, output = commands.getstatusoutput('sox ' + wave_file + ' -c 1 ' + mono_name)
	if status != 0:
		print "Problem with file ", wave_file[:-4]
		print "   Could not be converted to mono:,"
		print output
		print " "
		print "Exiting program."
		sys.exit()
	#
	return mono_name

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
	
	
#########################################################################
# EXECUTE THE SCRIPT							#
#########################################################################

#Get spectrogram palette
spectrogram_palette = getspectrogram_palette()

#Get all soundfiles
#results=getallsounds()

row=getsounds(SoundID)
#print str(SoundID) + "\n"
SoundID = str(int(SoundID))
ColID = row[1]
ColID = str(int(ColID))
DirID = row[2]
DirID = str(int(DirID))
filename = row[3]
SoundFormat = row[4]

item_flac = filename
c1=int(checkimages(SoundID))
c2=int(checkpngfiles(SoundID, ColID, DirID))
c3=int(checkmp3file(SoundID, ColID, DirID))
c4=c1 + c2 + c3

filez = check_filesize(SoundID)
if filez == 0:
	try:
		this_file_size = int(os.path.getsize(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac))
		insert_filesize(SoundID, this_file_size)
	except:
		print "\nFile not found\n"
		sys.exit(0)

if fileExists(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac)!=1:
	print "\nFile not found\n"
	sys.exit(0)
			
if c4!=13:
	if SoundFormat=="flac":
		item_wav = extractflac(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac)
	else:
		item_wav = extractsox(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac, SoundFormat)
	#Check if stereo
	no_channels = checkifstereo(item_wav)
	status, sampling_rate = commands.getstatusoutput('soxi -r ' + item_wav)
	
	nyquist = int(sampling_rate) / 2
	#Get max sampling rate to draw
	max_freq_draw=getmax_freq(nyquist)

	if no_channels == 2:
		delmysql(SoundID)
		draw_png_stereo(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
		insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)
	if no_channels == 1:
		delmysql(SoundID)
		draw_png(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
		insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)

	mp3newfile=0
	if c3!=1:
		mp3filename = makemp3(SoundID, item_wav, item_wav)
		insertmp3(SoundID, mp3filename)
		mp3newfile=1
								
	cleanup(server_dir, ColID, DirID, item_flac, 1, mp3newfile)
	
else:
	pngnewfile=0
	mp3newfile=0
	
	if c1!=6:
		if SoundFormat=="flac":
			item_wav = extractflac(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac)
		else:
			item_wav = extractsox(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac, SoundFormat)
		#Check if stereo
		no_channels = checkifstereo(item_wav)

		if no_channels == 2:
			delmysql(SoundID)
			draw_png_stereo(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
			insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)
		if no_channels == 1:
			delmysql(SoundID)
			draw_png(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
			insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)
		pngnewfile=1

	if c2!=6:
		if SoundFormat=="flac":
			item_wav = extractflac(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac)
		else:
			item_wav = extractsox(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac, SoundFormat)
		#Check if stereo
		no_channels = checkifstereo(item_wav)
			
		if no_channels == 2:
			delmysql(SoundID)
			draw_png_stereo(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
			insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)
		if no_channels == 1:
			delmysql(SoundID)
			draw_png(SoundID, item_flac, item_wav, sampling_rate, max_freq_draw, spectrogram_palette)
			insertpng(SoundID, item_flac, spectrogram_palette, max_freq_draw)
		pngnewfile=1

	if c3!=1:
		if SoundFormat=="flac":
			item_wav = extractflac(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac)
		else:
			item_wav = extractsox(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac, item_flac, SoundFormat)
		mp3filename = makemp3(SoundID, item_wav, item_flac)
		insertmp3(SoundID, mp3filename)
		mp3newfile=1

	cleanup(server_dir, ColID, DirID, item_flac, pngnewfile, mp3newfile)

c5 = int(checkmd5(SoundID))
if c5!=0:
	filemd5 = getmd5(server_dir + 'sounds/' + ColID + '/' + DirID + '/' + item_flac)
	insertmd5(SoundID, filemd5)

status, output = commands.getstatusoutput('rm -r ' + cur_dir)

sys.exit(0)
