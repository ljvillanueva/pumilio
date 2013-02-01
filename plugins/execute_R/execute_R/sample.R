#This is a sample script that can be run with the plugin option
#This script will open the file and display the sampling rate, nothing more.

#Get sampling rate
samplingrate <- SoundFile@samp.rate

cat(paste(" Sampling Rate: ", samplingrate, "<br>", sep=""))

