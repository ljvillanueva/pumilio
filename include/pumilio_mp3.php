<?php

#from http://www.walterzorn.com/jsgraphics/jsgraphics_e.htm
echo "\n<script type=\"text/javascript\">";
	#slight adjustment for timing, find a way to fix
	#echo "Hmove=-4;";
	$here_total_time=$time_max - $time_min;
	/*
	#Further tweak here or find better solution for line placement
	if ($here_total_time<30)
		echo "Hmove=-20;";
	elseif ($here_total_time<60)
		echo "Hmove=-6;";
	else
		echo "Hmove=-4;";
	*/
	echo "Hmove=0;";
	#function to move the indicator div using the css
	echo "
	function moveObjRight(draw) 
	{    
		if (draw<0)
			{draw=0;}
	
	   	Hmove2=Hmove+draw;
	   	myLine.style.marginLeft=Hmove2 + \"px\";
	   	myLine.style.visibility= \"visible\";
	};
	";

	#Javascript interaction for the JW flash player
	#From http://www.longtailvideo.com/support/forum/JavaScript-Interaction/12381/Read-the-playback-position

	#General calculations
	echo "
	var sound_total_time = $soundfile_duration;
	var total_time = $time_max - $time_min;
	var image_width = $spectrogram_width;
	var player = null;
	var time = null;
	var seek = 0;";

	#Get the name/id for JS
	echo "
	function gid(name)
		{
		return document.getElementById(name);
		};
	";

	#Once player is ready, add listener function
	echo "
	function playerReady(obj)
		{
		player = gid(obj.id);
		addListeners();
		};
	";


	echo "
	function addListeners()
		{
		player.addModelListener(\"TIME\", \"timeMonitor\");
		};
	";

	#Function to extract time position and display it as time and indicator line position
	echo "
	function timeMonitor(obj)
		{
			time = obj.position; //Get the current position
			time_current = time
			time = time+$time_min; //Add time_min to offset when zooming in a sound
			time_min1 = time/60;	//Calculations for current minute
			time_min = Math.floor(time/60);
			time_sec = time_min1-time_min;	//Calculations for current second
			time_sec = time_sec*60;
			time_sec = Math.round(time_sec*10)/10;

			//If seconds less than 10, add a leading 0 to keep proper display
			if (time_sec < 10)
			  {
			  time_sec = \"0\" + time_sec;
			  }

			//If seconds equal x.0, add trailing .0 to keep proper display
			if (time_sec==Math.round(time_sec))
			  {
			  time_sec = time_sec + \".0\";
			  }

			//Write the values in the appropriate divs
			//gid('time_min_div').innerHTML = time_min;	//write minutes
			//gid('time_sec_div').innerHTML = time_sec;	//write seconds
			//Using JQuery
			$(\"#time_min_div\").html(time_min);
			$(\"#time_sec_div\").html(time_sec);

			//Calculations to measure indicator line position
			/*
			if (xstart>0)
				{
				draw_time1 = (time-$time_min)/total_time;
				draw_time2 = ((draw_time1)/(sound_total_time/total_time))*image_width;
				}
			else
				{
			*/
				draw_time1 = (time_current-0.2)/total_time;";
				#The above correction to keep the indicator line in sync

		echo "draw_time2 = (draw_time1*image_width);
			//	}

			setTimeout(\"moveObjRight(draw_time2);\", 20);

			//If current time is more than xmax, stop
			if (xmax < time)
				{stop();}

			//If current time is less than the selected box, seek
			//if (xmin>time)
			//	{play(xmin);}

		};
	";

	#Pause function, send event to stop playing
	echo "
	function pause()
	      {
	        player.sendEvent('PLAY', 'false');
	      };
	";

	#play function, if seek is nonzero, go to that position. Delay for seek is necesary for player to properly work
	echo "
	function play(xstart)
	      {
		seek = xstart-$time_min;

		if (seek==0)
			{
				player.sendEvent('PLAY', 'true');
			}
		else
			{
				player.sendEvent('PLAY', 'true');
				setTimeout(\"player.sendEvent('SEEK', seek);\", 100);
			}
	      };
	";

	#Stop and hide the line
	echo "
	function stop()
	      {
	        player.sendEvent('STOP');
		myLine.style.visibility= \"hidden\";
	      };\n";

	echo "
	</script>
	";

#Player area
	#Containing Div
	echo "<div id=\"mp3player\" style=\"visibility:visible;\">You need to update the Adobe Flash Player to version 9 or higher and enable Javascript.</div>";

	#Javascript
	echo "
	<script type=\"text/javascript\">
	var so = new SWFObject('mediaplayer/player.swf','player','$spectrogram_width','1','9');
	so.addParam('allowscriptaccess','always');
	so.addVariable('duration','$player_file_duration');
	so.addVariable('file','tmp/$random_cookie/$player_file');
	so.addVariable('id','player');
	so.addVariable('volume','100');
	so.write('mp3player');
	</script>";
?>
