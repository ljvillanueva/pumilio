<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$settings = filter_var($_POST["settings"], FILTER_SANITIZE_STRING);

$force_admin = TRUE;
require("check_admin.php");

if ($settings == "top"){

	$value = filter_var($_POST["app_custom_name"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "app_custom_name"');
	$settings = array(
		'Settings' => 'app_custom_name',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'app_custom_name', 'Settings');
		}

			
	$value = filter_var($_POST["map_only"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "map_only"');
	$settings = array(
		'Settings' => 'map_only',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'map_only', 'Settings');
		}


	$value = filter_var($_POST["app_custom_text"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "app_custom_text"');
	$settings = array(
		'Settings' => 'app_custom_text',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'app_custom_text', 'Settings');
		}



	$value = filter_var($_POST["thanks_text"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "thanks_text"');
	$settings = array(
		'Settings' => 'thanks_text',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'thanks_text', 'Settings');
		}


	$value = filter_var($_POST["mapping_system"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "mapping_system"');
	$settings = array(
		'Settings' => 'mapping_system',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'mapping_system', 'Settings');
		}


	$value = filter_var($_POST["acknowledgement"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "acknowledgement"');
	$settings = array(
		'Settings' => 'acknowledgement',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'acknowledgement', 'Settings');
		}


	$value = filter_var($_POST["use_googlemaps"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "use_googlemaps"');
	$settings = array(
		'Settings' => 'use_googlemaps',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'use_googlemaps', 'Settings');
		}

		


	$value = filter_var($_POST["googleanalytics_ID"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "googleanalytics_ID"');
	$settings = array(
		'Settings' => 'googleanalytics_ID',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'googleanalytics_ID', 'Settings');
		}

			
			
	$value = filter_var($_POST["files_license"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "files_license"');
	$settings = array(
		'Settings' => 'files_license',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'files_license', 'Settings');
		}

			
	$value = filter_var($_POST["files_license_detail"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "files_license_detail"');
	$settings = array(
		'Settings' => 'files_license_detail',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'files_license_detail', 'Settings');
		}


	$value = filter_var($_POST["temp_add_dir"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "temp_add_dir"');
	$settings = array(
		'Settings' => 'temp_add_dir',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'temp_add_dir', 'Settings');
		}


	$value = filter_var($_POST["cores_to_use"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "cores_to_use"');
	$settings = array(
		'Settings' => 'cores_to_use',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'cores_to_use', 'Settings');
		}

			
	header("Location: ../admin.php?tt=1#gen");
	die();
	}
elseif ($settings == "image"){

	$value = filter_var($_POST["spectrogram_palette"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "spectrogram_palette"');
	$settings = array(
		'Settings' => 'spectrogram_palette',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'spectrogram_palette', 'Settings');
		}


	$value = filter_var($_POST["fft"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "fft"');
	$settings = array(
		'Settings' => 'fft',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'fft', 'Settings');
		}

	$value = filter_var($_POST["max_spec_freq"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "max_spec_freq"');
	$settings = array(
		'Settings' => 'max_spec_freq',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'max_spec_freq', 'Settings');
		}


	header("Location: ../admin.php?imgset=1#image");
	die();	
	}
elseif ($settings == "bottom"){

	$value = filter_var($_POST["use_chorus"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "use_chorus"');
	$settings = array(
		'Settings' => 'use_chorus',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'use_chorus', 'Settings');
		}


	$value = filter_var($_POST["use_xml"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "use_xml"');
	$settings = array(
		'Settings' => 'use_xml',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'use_xml', 'Settings');
		}

	$value = filter_var($_POST["xml_access"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "xml_access"');
	$settings = array(
		'Settings' => 'xml_access',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'xml_access', 'Settings');
		}


	$value = filter_var($_POST["use_tags"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "use_tags"');
	$settings = array(
		'Settings' => 'use_tags',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'use_tags', 'Settings');
		}

	$value = filter_var($_POST["hide_latlon_guests"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "hide_latlon_guests"');
	$settings = array(
		'Settings' => 'hide_latlon_guests',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'hide_latlon_guests', 'Settings');
		}


	$value = filter_var($_POST["sidetoside_comp"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "sidetoside_comp"');
	$settings = array(
		'Settings' => 'sidetoside_comp',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'sidetoside_comp', 'Settings');
		}

	$value = filter_var($_POST["allow_upload"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "allow_upload"');
	$settings = array(
		'Settings' => 'allow_upload',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'allow_upload', 'Settings');
		}

	$value = filter_var($_POST["wav_toflac"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "wav_toflac"');
	$settings = array(
		'Settings' => 'wav_toflac',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'wav_toflac', 'Settings');
		}

	$value = filter_var($_POST["guests_can_open"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "guests_can_open"');
	$settings = array(
		'Settings' => 'guests_can_open',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'guests_can_open', 'Settings');
		}


	$value = filter_var($_POST["guests_can_dl"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "guests_can_dl"');
	$settings = array(
		'Settings' => 'guests_can_dl',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'guests_can_dl', 'Settings');
		}

	$value = filter_var($_POST["default_qf"], FILTER_SANITIZE_STRING);
	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "default_qf"');
	$settings = array(
		'Settings' => 'default_qf',
		'Value' => $value
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'default_qf', 'Settings');
		}


	header("Location: ../admin.php?tt=2#sysb");
	die();
	}
elseif ($settings == "homelink"){

	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "btn1text"');

	$btn1text = filter_var($_POST["btn1text"], FILTER_SANITIZE_STRING);
	$btn1url = filter_var($_POST["btn1url"], FILTER_SANITIZE_URL);
	$btn2text = filter_var($_POST["btn2text"], FILTER_SANITIZE_STRING);
	$btn2url = filter_var($_POST["btn2url"], FILTER_SANITIZE_URL);
	$btn3text = filter_var($_POST["btn3text"], FILTER_SANITIZE_STRING);
	$btn3url = filter_var($_POST["btn3url"], FILTER_SANITIZE_URL);
	$btn4text = filter_var($_POST["btn4text"], FILTER_SANITIZE_STRING);
	$btn4url = filter_var($_POST["btn4url"], FILTER_SANITIZE_URL);

	$settings = array(
		'Settings' => 'btn1text',
		'Value' => $btn1text
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn1text', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn1url',
		'Value' => $btn1url
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn1url', 'Settings');
		}



	$settings = array(
		'Settings' => 'btn2text',
		'Value' => $btn2text
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn2text', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn2url',
		'Value' => $btn2url
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn2url', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn3text',
		'Value' => $btn3text
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn3text', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn3url',
		'Value' => $btn3url
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn3url', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn4text',
		'Value' => $btn4text
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn4text', 'Settings');
		}


	$settings = array(
		'Settings' => 'btn4url',
		'Value' => $btn4url
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'btn4url', 'Settings');
		}

	header("Location: ../admin.php#homelink");
	die();	
	}

	
header("Location: ../admin.php?t=1");
	die();

?>