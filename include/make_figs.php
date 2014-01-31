<?php

#First empty the db
$query_delete = "SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID'";
$result_delete=query_several($query_delete, $connection);
$nrows_delete = mysqli_num_rows($result_delete);
if ($nrows_delete>0) {
	for ($del=0;$del<$nrows_delete;$del++)	{
		$row_delete = mysqli_fetch_array($result_delete);
		extract($row_delete);
		
		$img_to_delete="sounds/images/" . $ColID . "/" . $DirID . "/" . $ImageFile;
		if (file_exists($img_to_delete))
			unlink($img_to_delete);
		}
	}

$query_delete_all = "DELETE FROM SoundsImages WHERE SoundID='$SoundID'";
$result_delete_all=query_several($query_delete_all, $connection);

if (!isset($OriginalFilename)) {
	$OriginalFilename = query_one("SELECT OriginalFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	}

$file=$OriginalFilename;

$fileName_exp=explode(".", $file);
$file2=$fileName_exp[0] . ".wav";

$file_format_pos= count($fileName_exp) - 1;
$file_format=$fileName_exp[$file_format_pos];

$random_value=mt_rand();
if (mkdir("tmp/$random_value", 0777)){

	#First, check if file exists
	if (!is_file("sounds/sounds/$ColID/$DirID/$OriginalFilename")) {
		$err_code="1";
		}
	else {
	if ($sox_images){
		#If a flac, extract
		$file2 = $file;
		copy('sounds/sounds/' . $ColID . '/' . $DirID . '/' . $file, 'tmp/' . $random_value . '/' . $file2);
		}
	else{
		#If a flac, extract
		if ($file_format=="flac") {
			exec('flac -fd sounds/sounds/' . $ColID . '/' . $DirID . '/' . $file . ' -o tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with the FLAC decoder...<br></div>");
			}
		else {
			exec('sox sounds/sounds/' . $ColID . '/' . $DirID . '/' . $file . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with SoX..</div>");
			}

		}

	#Get sampling rate
	$samp_rate = query_one("SELECT SamplingRate FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$nyquist_freq = $samp_rate/2;

		
	#Get the max freq to draw from db
	$max_spec_freq = query_one("SELECT Value from PumilioSettings WHERE Settings='max_spec_freq'", $connection);

	$max_spec_freq_rate = round(($max_spec_freq * 2) / 1000, 2);

	if ($max_spec_freq=="max") {
		$max_spec_freq=$nyquist_freq;
		}
	elseif ($max_spec_freq=="") {
		$max_spec_freq=22050;
		}

	$half_max_spec_freq = $max_spec_freq/2;
	$max_spec_freq_t = $max_spec_freq . " Hz";
	$half_max_spec_freq_t = $half_max_spec_freq . " Hz";


	#Check if dir exists
	if (!is_dir("sounds/images/$ColID")) {
		mkdir("sounds/images/$ColID", 0777);
		}
	if (!is_dir("sounds/images/$ColID/$DirID")) {
		mkdir("sounds/images/$ColID/$DirID", 0777);
		}

	$spectrogram_palette=query_one("SELECT Value FROM PumilioSettings WHERE Settings='spectrogram_palette' LIMIT 1", $connection);
	
	if ($sox_images){
		#Palette to use
		if ($spectrogram_palette == "") {
			if ($spectrogram_palette < 1 || $spectrogram_palette > 6) {
				$spectrogram_palette=6;
				}
			}
		else {
			$spectrogram_palette=6;
			}

		if ($spectrogram_palette==1) {
			$letter_color="black";
			}
		elseif ($spectrogram_palette==2) {
			$letter_color="black";
			}
		elseif ($spectrogram_palette==3) {
			$letter_color="black";
			}
		elseif ($spectrogram_palette==4) {
			$letter_color="black";
			}
		elseif ($spectrogram_palette==5) {
			$letter_color="black";
			}
		elseif ($spectrogram_palette==6) {
			$letter_color="black";
			}
		}
	else {
		#Palette to use
		if ($spectrogram_palette == "") {
			if ($spectrogram_palette!=1 || $spectrogram_palette!=2) {
				$spectrogram_palette=2;
				}
			}
		else {
			$spectrogram_palette=2;
			}

		if ($spectrogram_palette==1) {
			$letter_color="white";
			}
		elseif ($spectrogram_palette==2) {
			$letter_color="black";
			}
		}


	#IMAGE SIZES
	# small = 300x150
	# mid = 600x300
	# large = 920x460					
	$image_width_small = 300;
	$image_height_small = 150;
	$image_width_med = 600;
	$image_height_med = 300;
	$image_width_large = 920;
	$image_height_large = 460;


	#MED Size images
	$random_dir=mt_rand();
	mkdir("tmp/$random_dir", 0777);

	if (!isset($Channels)) {
		$Channels=query_one("SELECT Channels FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		}

	if ($Channels==1) {
		if ($sox_images){
			$image_height_med1 = $image_height_med + 1;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_med . ' -y ' . $image_height_med1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png -crop ' . $image_width_med . 'x' . $image_height_med . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '_s1.png', $lastline, $retval);
			if ($retval!=0){
				die('<div class=\"error\">There was a problem with Imagemagick...</div>');
				}
			}
		else{
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_s1.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_w1.png -w ' . $image_width_med . ' -o 1 -h ' . $image_height_med . ' -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with svt...</div>");
			}

		if ($sox_images == FALSE){
			#Quality to resize
			exec("convert tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w.png", $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
			
		#Draw the max freq
		exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" -draw \"text 5,155 '" . $half_max_spec_freq_t . "'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s.png", $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");
			
		}
	elseif ($Channels==2) {
		#left
		$image_height_med = $image_height_med / 2;
		
		if ($sox_images){
			$image_height_med1 = $image_height_med + 1;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_med . ' -y ' . $image_height_med1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_med . 'x' . $image_height_med . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png', $lastline, $retval);
			if ($retval!=0){
				die('<div class=\"error\">There was a problem with Imagemagick...</div>');
				}
			
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_med . 'x' . $image_height_med . '+0+' . $image_height_med . ' tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png', $lastline, $retval);
			if ($retval!=0){
				die('<div class=\"error\">There was a problem with Imagemagick...</div>');
				}
			}
		else {
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png -w ' . $image_width_med . ' -o 1 -h ' . $image_height_med . ' -c 1 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with svt...</div>");

			#right
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png -w ' . $image_width_med . ' -o 1 -h ' . $image_height_med . ' -c 2 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with svt...</div>");
			}
		

		#combine spectrograms
		exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_s1.png', $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");

		exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" -draw \"text 5,165 '" . $max_spec_freq_t . "'\" -draw \"text 590,15 'L'\" -draw \"text 590,165 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s.png", $lastline, $retval);
		if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");
			
		if ($sox_images == FALSE){
			#combine waveforms
			exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_w1.png', $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");

			exec("convert -fill " . $letter_color . " -draw \"text 590,15 'L'\" -draw \"text 590	,165 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w.png", $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");

			}
		}

	#copy spectrogram
	$s_file=$fileName_exp[0] . "_s.png";
	$w_file=$fileName_exp[0] . "_w.png";
	$s_file_done=$SoundID . "_s.png";
	$w_file_done=$SoundID . "_w.png";	

	$from_file_s="tmp/" . $random_dir . "/" . $s_file;
	$from_file_w="tmp/" . $random_dir . "/" . $w_file;

	$where_to_s="sounds/images/" . $ColID . "/" . $DirID . "/" . $s_file_done;
	$where_to_w="sounds/images/" . $ColID . "/" . $DirID . "/" . $w_file_done;

	if ($sox_images == TRUE){
		$ImageCreator = "SoX";
		}
	else{
		$ImageCreator = "svt";
		}
		
	copy($from_file_s,$where_to_s);
	$query_imgs = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette,SpecMaxFreq,ImageCreator,ImageFFT) 
				VALUES ('$SoundID', '$s_file_done', 'spectrogram', '$spectrogram_palette', '$max_spec_freq', '$ImageCreator', '$fft')";
		$result_imgw = mysqli_query($connection, $query_imgs)
			or die (mysqli_error($connection));

	if ($sox_images == FALSE){
		copy($from_file_w,$where_to_w);
		$query_imgw = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette) 
					VALUES ('$SoundID', '$w_file_done', 'waveform', '$spectrogram_palette')";
		$result_imgw = mysqli_query($connection, $query_imgw)
			or die (mysqli_error($connection));
		}

	delTree("tmp/" . $random_dir);


	#SMALL Size images
	$random_dir=mt_rand();
	mkdir("tmp/$random_dir", 0777);

	if ($Channels==1) {
		if ($sox_images){
			$image_height_small1 = $image_height_small + 1;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_small . ' -y ' . $image_height_small1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png -crop ' . $image_width_small . 'x' . $image_height_small . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '-small_s1.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
		else{
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '-small_s1.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '-small_w1.png -w ' . $image_width_small . ' -o 1 -h ' . $image_height_small . ' -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with svt...</div>");
				}
			}
			
		if ($sox_images == FALSE){
			#Quality to resize
			exec("convert tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_w.png", $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}

		#Draw the max freq
		exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_s.png", $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");
			
		}
	elseif ($Channels==2) {
		$image_height_small = $image_height_small / 2;
		
		if ($sox_images){
			$image_height_small1 = $image_height_small + 1;
			$image_height_small2 = $image_height_small * 2;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_small . ' -y ' . $image_height_small1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_small . 'x' . $image_height_small . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
				
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_small . 'x' . $image_height_small . '+0+' . $image_height_small . ' tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
		else{
			#left
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png -w ' . $image_width_small . ' -o 1 -h ' . $image_height_small . ' -c 1 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with svt...</div>");

			#right
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png -w ' . $image_width_small . ' -o 1 -h ' . $image_height_small . ' -c 2 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with svt...</div>");
			}
			
		#combine spectrograms
		exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_s1.png', $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");

			exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" -draw \"text 5,85 '" . $max_spec_freq_t . "'\" -draw \"text 290,15 'L'\" -draw \"text 290,85 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_s.png", $lastline, $retval);
			if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
			
		if ($sox_images == FALSE){
			#combine waveforms
			exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_w1.png', $lastline, $retval);
			if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				
			exec("convert -fill " . $letter_color . " -draw \"text 290,15 'L'\" -draw \"text 290,85 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-small_w.png", $lastline, $retval);
			if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");				
			}
		}

	#copy spectrogram
	$s_file=$fileName_exp[0] . "-small_s.png";
	$w_file=$fileName_exp[0] . "-small_w.png";
	$s_file_done=$SoundID . "-small_s.png";
	$w_file_done=$SoundID . "-small_w.png";

	$from_file_s="tmp/" . $random_dir . "/" . $s_file;
	$from_file_w="tmp/" . $random_dir . "/" . $w_file;

	$where_to_s="sounds/images/" . $ColID . "/" . $DirID . "/" . $s_file_done;
	$where_to_w="sounds/images/" . $ColID . "/" . $DirID . "/" . $w_file_done;

	if ($sox_images == TRUE){
		$ImageCreator = "SoX";
		}
	else{
		$ImageCreator = "svt";
		}

	copy($from_file_s,$where_to_s);
	
	$query_imgs = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette,SpecMaxFreq,ImageCreator,ImageFFT) 
				VALUES ('$SoundID', '$s_file_done', 'spectrogram-small', '$spectrogram_palette', '$max_spec_freq','$ImageCreator','$fft')";
	$result_imgw = mysqli_query($connection, $query_imgs)
		or die (mysqli_error($connection));
	
	if ($sox_images == FALSE){
		copy($from_file_w,$where_to_w);

		$query_imgw = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette) 
				VALUES ('$SoundID', '$w_file_done', 'waveform-small', '$spectrogram_palette')";
		$result_imgw = mysqli_query($connection, $query_imgw)
			or die (mysqli_error($connection));
		}

	delTree("tmp/" . $random_dir);


	#LARGE Size images
	$random_dir=mt_rand();
	mkdir("tmp/$random_dir", 0777);

	if ($Channels==1) {
		if ($sox_images){
			$image_height_large1 = $image_height_large + 1;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_large . ' -y ' . $image_height_large1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_st1.png -crop ' . $image_width_large . 'x' . $image_height_large . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '-large_s1.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
		else{
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '-large_s1.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '-large_w1.png -w ' . $image_width_large . ' -o 1 -h ' . $image_height_large . ' -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with svt...</div>");
				}
			}
		
		if ($sox_images == FALSE){
			#Quality to resize
			exec("convert tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_w.png", $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
		
		#Draw the max freq
		exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" -draw \"text 5,235 '" . $half_max_spec_freq_t . "'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_s.png", $lastline, $retval);
			if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");
		}
	elseif ($Channels==2) {
		$image_height_large = $image_height_large / 2;
		if ($sox_images){
			$image_height_large1 = $image_height_large + 1;
			$image_height_large2 = $image_height_large * 2;
			exec('sox tmp/' . $random_value . '/' . $file2 . ' -n rate ' . $max_spec_freq_rate . 'k spectrogram -x ' . $image_width_large . ' -y ' . $image_height_large1 . ' -a -r -l -p ' . $spectrogram_palette . ' -o tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with SoX...</div>");
				}
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_large . 'x' . $image_height_large . '+0+0 tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
				
			#Trim
			exec('convert tmp/' . $random_dir . '/' . $fileName_exp[0] . '_lt_s.png -crop ' . $image_width_large . 'x' . $image_height_large . '+0+' . $image_height_large . ' tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png', $lastline, $retval);
			if ($retval!=0){
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
				}
			}
		else{
			#left
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png -w ' . $image_width_large . ' -o 1 -h ' . $image_height_large . ' -c 1 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with svt...</div>");

			#right
			exec('include/svt.py -f ' . $fft . ' -s tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png -a tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png -w ' . $image_width_large . ' -o 1 -h ' . $image_height_large . ' -c 2 -m ' . $max_spec_freq . ' -p ' . $spectrogram_palette . ' tmp/' . $random_value . '/' . $file2, $lastline, $retval);
				if ($retval!=0)
				die("<div class=\"error\">There was a problem with svt...</div>");
			}
			
		#combine spectrograms
		exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_s.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_s1.png', $lastline, $retval);
		if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");

		exec("convert -fill " . $letter_color . " -draw \"text 5,15 '" . $max_spec_freq_t . "'\" -draw \"text 5,245 '" . $max_spec_freq_t . "'\" -draw \"text 905,15 'L'\" -draw \"text 905,245 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_s1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_s.png", $lastline, $retval);
		if ($retval!=0)
			die("<div class=\"error\">There was a problem with Imagemagick...</div>");

		if ($sox_images == FALSE){
			#combine waveforms
			exec('montage -tile 1x2 -mode Concatenate tmp/' . $random_dir . '/' . $fileName_exp[0] . '_l_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_r_w.png tmp/' . $random_dir . '/' . $fileName_exp[0] . '_w1.png', $lastline, $retval);
			if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
		
			exec("convert -fill " . $letter_color . " -draw \"text 905,15 'L'\" -draw \"text 905,245 'R'\" tmp/" . $random_dir . "/" . $fileName_exp[0] . "_w1.png -quality 10 tmp/" . $random_dir . "/" . $fileName_exp[0] . "-large_w.png", $lastline, $retval);
			if ($retval!=0)
				die("<div class=\"error\">There was a problem with Imagemagick...</div>");
			}
		}

	#copy spectrogram
	$s_file=$fileName_exp[0] . "-large_s.png";
	$w_file=$fileName_exp[0] . "-large_w.png";
	$s_file_done=$SoundID . "-large_s.png";
	$w_file_done=$SoundID . "-large_w.png";

	$from_file_s="tmp/" . $random_dir . "/" . $s_file;
	$from_file_w="tmp/" . $random_dir . "/" . $w_file;

	$where_to_s="sounds/images/" . $ColID . "/" . $DirID . "/" . $s_file_done;
	$where_to_w="sounds/images/" . $ColID . "/" . $DirID . "/" . $w_file_done;

	if ($sox_images == TRUE){
		$ImageCreator = "SoX";
		}
	else{
		$ImageCreator = "svt";
		}
		
	copy($from_file_s,$where_to_s);

	$query_imgs = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette,SpecMaxFreq,ImageCreator,ImageFFT) 
				VALUES ('$SoundID', '$s_file_done', 'spectrogram-large', '$spectrogram_palette', '$max_spec_freq','$ImageCreator','$fft')";
	$result_imgw = mysqli_query($connection, $query_imgs)
		or die (mysqli_error($connection));
				
	if ($sox_images == FALSE){
		copy($from_file_w,$where_to_w);
		$query_imgw = "INSERT INTO SoundsImages (SoundID,ImageFile,ImageType,ColorPalette) 
			VALUES ('$SoundID', '$w_file_done', 'waveform-large', '$spectrogram_palette')";
		$result_imgw = mysqli_query($connection, $query_imgw)
			or die (mysqli_error($connection));
		}

	delTree("tmp/" . $random_dir);
	}
	
#Del temp sound file
delTree("tmp/" . $random_value);
}
else {
	echo "<div class=\"error\">Could not create temporary folder.</div>";
	}

if (isset($err_code)) {
	if ($err_code=="1") {
		echo "<div class=\"error\">The original sound file could not be found. Sound ID: $SoundID</div>";
		}
	}

?>
