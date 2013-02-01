##Load packages
require(DBI)
require(RMySQL)
require(tuneR)
require(seewave)
require(ineq)
drv <- dbDriver("MySQL")

#Get command line arguments
## break each in python or bash
args <- commandArgs(TRUE)
	filename = args[1]
	SoundID = args[2]
	db_hostname = args[3]
	db_database = args[4]
	db_username = args[5]
	db_password = args[6]
	db_value = as.numeric(args[7])
	max_freq = as.numeric(args[8])
	freq_step = as.numeric(args[9])
	segment_length = as.numeric(args[10])


#Clean table
	con <- dbConnect(drv, user = db_username, password = db_password, host = db_hostname, dbname = db_database)

	#clean database
	query_to_del<-paste("DELETE FROM SoundsStatsResults WHERE SoundID='", SoundID, "'", sep="")
	res <- dbGetQuery(con, query_to_del)
	
	#close MySQL connection
	invisible(dbDisconnect(con))


#Functions
## Function that returns two values: ADI and Gini
acoustic_diversity<-function(soundfile, db_threshold=-50, save_to_text=FALSE, max_freq=10000, freq_step=1000){
	#function that gets the % of values over a db value in a specific band
	# frequency is in Hz
	getscore<-function(specA, minf, maxf, db, freq_row){
		miny<-round((minf)/freq_row)
		maxy<-round((maxf)/freq_row)
		
		subA=specA[miny:maxy,]
		
		index1<-length(subA[subA>db])/length(subA)
		
		return(index1)
	}
	
	#check variables and packages
	check_val = 0
	
	if (db_threshold > 0) {
		check_val=1
	}
	
	if (db_threshold < -100) {
		check_val=2
	}
	
	#all checks passed, run script
	if (check_val==0) {
		#Some general values
		#Get sampling rate
		samplingrate<-soundfile@samp.rate
		
		#Get Nyquist frequency in Hz
		nyquist_freq<-(samplingrate/2)
		
		#window length for the spectro and spec functions
		#to keep each row every 10Hz
		#Frequencies and seconds covered by each
		freq_per_row = 10
		wlen=samplingrate/freq_per_row
		
		#matrix of values
		specA_left <- spectro(soundfile, f=samplingrate, wl=wlen, plot=FALSE)$amp
		
		rm(soundfile)
		
		if (max_freq>nyquist_freq) {
			cat(1)
			q(save = "no", status = 0, runLast = FALSE)
		}
		
		Freq<-seq(from=0, to=max_freq-freq_step, by=freq_step)
		
		#Score=seq(from=0, to=0, length=length(Freq))
		Score <- rep(NA, length(Freq))
		
		for (j in 1:length(Freq)) {
			Score[j]=getscore(specA_left, Freq[j], (Freq[j]+freq_step), db_threshold, freq_step)
		}
		
		left_vals=Score
		
		Score1=0
		for (i in 1:length(Freq)) {
			Score1=Score1 + (Score[i] * log(Score[i]+0.0000001))
		}
		
		#Average
		Score_left=(-(Score1))/length(Freq)
		
		left_adi_return = round(Score_left,6)
		left_gini_return = round(Gini(left_vals),6)
		
	} else 
	{
		#some check failed, print error
		if (check_val==1) {
			cat(2)
			q(save = "no", status = 0, runLast = FALSE)
		}
		
		if (check_val==2) {
			cat(3)
			q(save = "no", status = 0, runLast = FALSE)
		}
		
	}
	return(c(left_adi_return, left_gini_return))
}



#Open file
#cat(paste("\n Opening sound file ", filename, "\n\n", sep=""))

soundfile <- readWave(filename)

#Get sampling rate
samplingrate <- soundfile@samp.rate

#Get Nyquist frequency in Hz
nyquist_freq<-(samplingrate/2)

#Get maximum frequency in Hz
maximum_freq <- samplingrate/2

	if (max_freq > maximum_freq) {
		cat(5)
		q(save = "no", status = 0, runLast = FALSE)
	}

#window length for the spectro and spec functions
#to keep each row every 10Hz
wlen = samplingrate/10

this_results <- data.frame()

if (soundfile@stereo==TRUE) {
	left_channel<-channel(soundfile, which = "left")
	right_channel<-channel(soundfile, which = "right")
	rm(soundfile)
	
	duration <- length(left_channel)/samplingrate

	#diffspec (Difference between two frequency spectra, seewave)
	diff_spec <- diffspec(spec(left_channel, plot=FALSE), spec(right_channel, plot=FALSE))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, "diffspec", round(diff_spec, 6))
	this_results <- rbind(this_results, cbind("diffspec", round(diff_spec, 6)))

	#H (Total entropy, seewave) - dropped, takes too long and too much memory
	#sound_H_l <- H(left_channel)	
	#	segments = ceiling(duration / segment_length)
	#	A=0
	#	if(segments > 1){
	#		for (i in 1:(segments-1)) {
	#			A[i] <- H(cutw(left_channel, from=((i-1) * segment_length), to=(i * segment_length), output="Wave"))
	#		}
	#		A[i+1] <- H(cutw(left_channel, from=(i * segment_length), to=duration, output="Wave"))
	#		meanA <- mean(A)
	#	} else {
	#		meanA <- H(left_channel)
	#	}
	#	sound_H_l <- meanA
	#	rm(A)
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, "H_left", round(sound_H, 6))
	#this_results <- rbind(this_results, cbind(paste("meanH_", segment_length, "_left", sep=""), round(sound_H_l, 6)))

	#sound_H_r <- H(right_channel)
	#	segments = ceiling(duration / segment_length)
	#	A=0
	#	if(segments > 1){
	#		for (i in 1:(segments-1)) {
	#			A[i] <- H(cutw(left_channel, from=((i-1) * segment_length), to=(i * segment_length), output="Wave"))
	#		}
	#		A[i+1] <- H(cutw(left_channel, from=(i * segment_length), to=duration, output="Wave"))
	#		meanA <- mean(A)
	#	} else {
	#		meanA <- H(right_channel)
	#	}
	#	sound_H_r <- meanA
	#	rm(A)
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, "H_right", round(sound_H, 6))
	#this_results <- rbind(this_results, cbind(paste("meanH_", segment_length, "_right", sep=""), round(sound_H_r, 6)))
	#this_results <- rbind(this_results, cbind(paste("meanH_", segment_length, "_difference", sep=""), abs(round(sound_H_r, 6) - round(sound_H_l, 6))))

	ADI_Gini_left <- acoustic_diversity(left_channel, db_value, FALSE, max_freq, freq_step)
	ADI_Gini_right <- acoustic_diversity(right_channel, db_value, FALSE, max_freq, freq_step)

	#ADI
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[1], 6))
	this_results <- rbind(this_results, cbind(paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[1], 6)))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_right", sep=""), round(ADI_Gini_right[1], 6))
	this_results <- rbind(this_results, cbind(paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_right", sep=""), round(ADI_Gini_right[1], 6)))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_difference", sep=""), round(abs(ADI_Gini_left[1]-ADI_Gini_right[1]), 6))
	this_results <- rbind(this_results, cbind(paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_difference", sep=""), round(abs(ADI_Gini_left[1]-ADI_Gini_right[1]), 6)))

	#Gini
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[2], 6))
	this_results <- rbind(this_results, cbind(paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[2], 6)))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_right", sep=""), round(ADI_Gini_right[2], 6))
	this_results <- rbind(this_results, cbind(paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_right", sep=""), round(ADI_Gini_right[2], 6)))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_difference", sep=""), round(abs(ADI_Gini_left[2]-ADI_Gini_right[2]), 6))
	this_results <- rbind(this_results, cbind(paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_difference", sep=""), round(abs(ADI_Gini_left[2]-ADI_Gini_right[2]), 6)))
} else{
	left_channel<-channel(soundfile, which = "left")
	rm(soundfile)
	duration <- length(left_channel)/samplingrate

	#H (Total entropy, seewave)
		#sound_H <- H(left_channel)
	#	segments = ceiling(duration / segment_length)
	#	A=0
	#	if(segments > 1){
	#		for (i in 1:(segments-1)) {
	#			A[i] <- H(cutw(left_channel, from=((i-1) * segment_length), to=(i * segment_length), output="Wave"))
	#		}
	#		A[i+1] <- H(cutw(left_channel, from=(i * segment_length), to=duration, output="Wave"))
	#		meanA <- mean(A)
	#	} else {
	#		meanA <- H(left_channel)
	#	}
	#	sound_H <- meanA
	#	rm(A)
		
	#this_results <- rbind(this_results, cbind(paste("meanH_", segment_length, "_left", sep=""), round(sound_H, 6)))
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, "H_left", round(sound_H, 6))

	ADI_Gini_left <- acoustic_diversity(left_channel, db_value, FALSE, max_freq, freq_step)

	#ADI
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[1], 6))
	this_results <- rbind(this_results, cbind(paste("ADI_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[1], 6)))
	
	#Gini
	#toMySQL(SoundID, db_hostname, db_database, db_username, db_password, paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[2], 6))
	this_results <- rbind(this_results, cbind(paste("Gini_", freq_step, "_", max_freq, "_", db_value, "_left", sep=""), round(ADI_Gini_left[2], 6)))
	}


this_results <- cbind("NULL", SoundID, this_results)

colnames(this_results) <- c("SoundsStatsID", "SoundID", "Stat", "StatValue")

#MySQL
	con <- dbConnect(drv, user = db_username, password = db_password, host = db_hostname, dbname = db_database)
	#insert in database
	ret_value <- dbWriteTable(con, 'SoundsStatsResults', this_results, row.names = F, overwrite = F, append=T)
	#dbCommit(con)
	#close MySQL connection
	invisible(dbDisconnect(con))

#Cleanup
file_name <- strsplit(filename, "\\.")[[1]]
unlink(paste(file_name[1], "*", sep=""))

#Exit
cat(0)
