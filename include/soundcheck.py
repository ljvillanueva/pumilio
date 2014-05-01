#!/usr/bin/env python
 
# Script to get basic info on the file
#uses soxi or metaflac instead of audiolab

import sys, commands

# Test command line variables
if len(sys.argv) != 2:
	print " "
	print "Incorrect number of arguments given."
	print "Call the program as: " + sys.argv[0] + " <file>"
	print " "
	print "Exiting program."
	print " "
	sys.exit()

# Place "global" variables in the namespace
file_2_check = sys.argv[1]


#First check if FLAC, audiolab does not support flac yet
# if file_2_check[-5:] == ".flac" or file_2_check[-5:] == ".FLAC":

# 	status, no_channels = commands.getstatusoutput('metaflac --show-channels ' + file_2_check)
# 	if status != 0:
# 		sys.exit(1)
# 	status, total_samples = commands.getstatusoutput('metaflac --show-total-samples ' + file_2_check)
# 	status, sampling_rate = commands.getstatusoutput('metaflac --show-sample-rate ' + file_2_check)
# 	fileformat = "FLAC"
# 	duration = float(total_samples) / float(sampling_rate)

# else:

status, sampling_rate = commands.getstatusoutput('soxi -r ' + file_2_check)
if status != 0:
	sys.exit(1)
#if above worked, the rest should
status, no_channels = commands.getstatusoutput('soxi -c ' + file_2_check)
status, fileformat = commands.getstatusoutput('soxi -t ' + file_2_check)
status, duration = commands.getstatusoutput('soxi -d ' + file_2_check)
status, bitrate = commands.getstatusoutput('soxi -b ' + file_2_check)
duration=duration.split(":")
duration = float(duration[2]) + (float(duration[1])*60) + (float(duration[0])*3600)

# Sndfile instances can be queried for the audio file meta-data
#sampling_rate = f.samplerate
#no_channels = f.channels
#enc = f.encoding
#fileformat = f.file_format
#total_samples = f.nframes
#duration = round((total_samples + 0.0) / int(sampling_rate), 2)



print str(sampling_rate) + "," + str(no_channels) + "," + str.lower(str(fileformat)) + "," + str(duration) + "," + str(bitrate)
