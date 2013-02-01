<?php

#Update the list of sites
#Redundant - for now, to be fixed in a future release
$query_of_sites="SELECT DISTINCT Location, Latitude, Longitude FROM Sounds WHERE Latitude IS NOT NULL AND Longitude IS NOT NULL";
$result_of_sites = mysqli_query($connection, $query_of_sites)
			or die (mysqli_error($connection));
$nrows_of_sites = mysqli_num_rows($result_of_sites);
for ($s=0;$s<$nrows_of_sites;$s++) {
	$row_of_sites = mysqli_fetch_array($result_of_sites);
	extract($row_of_sites);
	$is_site=query_one("SELECT COUNT(*) FROM Sites WHERE SiteLat LIKE '$Latitude' AND SiteLon LIKE '$Longitude'", $connection);
	if ($is_site==0) {
		$querys = "INSERT INTO Sites (SiteName,SiteLat,SiteLon) VALUES ('$Location', '$Latitude', '$Longitude')";
		$results = mysqli_query($connection, $querys)
			or die (mysqli_error($connection));
		}
	}

#Update SiteID
$query_sites="SELECT * FROM Sites";
$result_sites = mysqli_query($connection, $query_sites)
			or die (mysqli_error($connection));
$nrows_sites = mysqli_num_rows($result_sites);
if ($nrows_sites>0) {
	for ($s1=0;$s1<$nrows_sites;$s1++) {
		$row_sites = mysqli_fetch_array($result_sites);
		extract($row_sites);

		$query_sites1="UPDATE Sounds SET SiteID=$SiteID WHERE Latitude LIKE '$SiteLat' AND Longitude LIKE '$SiteLon'";
		$result_sites1 = mysqli_query($connection, $query_sites1)
			or die (mysqli_error($connection));
		}
	}
?>
