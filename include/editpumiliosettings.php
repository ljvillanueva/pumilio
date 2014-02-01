<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$Settings = filter_var($_POST["Settings"], FILTER_SANITIZE_STRING);
$Value = filter_var($_POST["Value"], FILTER_SANITIZE_STRING);
$settings = filter_var($_POST["settings"], FILTER_SANITIZE_STRING);

$force_admin = TRUE;
require("check_admin.php");

	if ($settings == "top"){
		$value = filter_var($_POST["app_custom_name"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["map_only"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('map_only', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["app_custom_text"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["use_googlemaps"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["googlemaps3_key"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps3_key', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["files_license"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('files_license', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["files_license_detail"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('files_license_detail', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["temp_add_dir"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('temp_add_dir', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["cores_to_use"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('cores_to_use', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);

		header("Location: ../admin.php?t=1&tt=1");
		die();
		}
	elseif ($settings == "image"){
	
		$value = filter_var($_POST["spectrogram_palette"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["fft"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('fft', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);

		$value = filter_var($_POST["max_spec_freq"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('max_spec_freq', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);

		header("Location: ../admin.php?t=1&imgset=1");
		die();	
		}
	elseif ($settings == "bottom"){
		$value = filter_var($_POST["use_chorus"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_chorus', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["use_xml"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_xml', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["xml_access"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('xml_access', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["use_tags"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_tags', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["hide_latlon_guests"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["sidetoside_comp"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('sidetoside_comp', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["allow_upload"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["wav_toflac"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('wav_toflac', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["guests_can_open"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["guests_can_dl"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_dl', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
				
		$value = filter_var($_POST["default_qf"], FILTER_SANITIZE_STRING);
		query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('default_qf', '$value') 
				ON DUPLICATE KEY UPDATE Value='$value'", $connection);
		
		header("Location: ../admin.php?t=1&tt=2");
		die();
		}
	elseif ($Settings == "files_license"){
		$files_license_detail = filter_var($_POST["files_license_detail"], FILTER_SANITIZE_STRING);
		$query = "REPLACE INTO PumilioSettings (Settings, Value) VALUES ('$Settings', '$Value')";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$query = "REPLACE INTO PumilioSettings (Settings, Value) VALUES ('files_license_detail', '$files_license_detail')";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		header("Location: ../admin.php?t=1");
			die();
		}
	else{
		#check if it exists
		$check=query_one("SELECT COUNT(*) from PumilioSettings WHERE Settings='$Settings'", $connection);
		if ($check==0) {
			$query = "INSERT INTO PumilioSettings (Settings, Value) VALUES ('$Settings', '$Value')";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			}
		else {
			$query = "UPDATE PumilioSettings SET Value='$Value' WHERE Settings='$Settings'";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			}
		}
		
header("Location: ../admin.php?t=1");
	die();

?>
