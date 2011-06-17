﻿<html>
<head>

<style>

body
{
	padding: 15;
	margin: 15;
	font-family: Georgia, Times, serif;
	background-color: #ffffff;
	width: 70%;
}

code {
	margin: 10px 10px 10px 0px;
	padding: 10px;
	border: 1px solid #CCC;
	background-color: #CCF;
	font-size:1.2em;
	display: block
}

li {
	margin-bottom: 30px;
	
}

</style>

</head>
<body>

<h1>Pachube PHP Library functions</h1>

<p><i><a href="http://www.pachube.com/">Pachube</a> is a web service that enables you to connect, tag and share real time sensor data from objects, devices, buildings and environments around the world. The key aim is to facilitate interaction between remote environments, both physical and virtual.</i></p>
<ul>

<?php


require_once( 'PachubeAPI.php' );


# *****************************************************************
#
# Create a Pachube object and pass your API key
#
# *****************************************************************


$api_key = "YOUR_KEY";

echo "<hr>";
echo "<li><b>Create a Pachube object with your API key: </b>";
echo '<code>$pachube = new Pachube($api_key); </code>';

$pachube = new PachubeAPI($api_key);


# *****************************************************************
#
# working with Environments (returns an associative array)
#
# *****************************************************************

echo "</li><hr>";
echo "<li><b>Work with an environment (returned as an associative array): </b><br>";

$feed_id = 25842;
$environment = $pachube->getEnvironment( $feed_id );

echo '<code>$environment = $pachube->environment( $feed_id ); </code>';

echo "\$environment['description']: ".$environment['description']."<br>";
echo "\$environment['status']: ".$environment['status']."<br>";
echo "\$environment['location']['domain']: ".$environment['location']['domain']."<br>";
echo "\$environment['location']['exposure']: ".$environment['location']['exposure']."<br>";
echo "\$environment['location']['disposition']: ".$environment['location']['disposition']."<br>";
echo "count(\$environment['datastreams']): ".count($environment['datastreams'])."<br>";
echo "\$environment['datastreams']['0']['values'][0]['value']: ".$environment['datastreams']['0']['values'][0]['value']."<br>";


echo "</li><hr>";
echo "<li><b>Create graphs and maps using the \$environment array: </b><br>";
echo '<code>$pachube->showEnvironmentGraph($environment,1); </code>';
$pachube->showEnvironmentGraph($environment,1);
echo "<br>";
echo '<code>$pachube->showEnvironmentGraph($environment,2, 700, 250, "0000FF", false, false, "My configured graph title", "My datastream units", 6); </code>';
$pachube->showEnvironmentGraph($environment,2, 700, 250, "0000FF", false, false, "My configured graph title", "My datastream units", 6);
echo "<br>";

// the following requires you to have a Google Map API key for your domain available here: http://code.google.com/apis/maps/

echo '<code>$pachube->showEnvironmentMap($environment, 500, 200, "GOOGLE_MAP_API_KEY"); </code>';
$pachube->showEnvironmentMap($environment, 500, 200, "ABQIAAAAYGdShHJUqUUqCZujCgqoyxRhf0yX7jCDZEW8LvcORLdH4560mRQtTT3Vx6wORcHDcMrtNf9XNlmO0w");




#/*
# *****************************************************************
#
# retrieve Pachube feed data
#
# *****************************************************************

echo "</li><hr>";
echo "<li><b>Retrieve raw feed data via URL (e.g. http://www.pachube.com/api/504.csv [.json | .xml]): </b>";
$url = "http://www.pachube.com/api/504.csv";
$data = $pachube->getFeedData( $url );
echo '<code>$data = $pachube->getFeedData ($url); </code>';
echo $data;

echo "</li><hr>";
echo "<li><b>Retrieve raw feed data as CSV, using feed ID only: </b>";
$feed = 480;
$data = $pachube->getFeedData ( $feed, "csv" );
echo '<code>$data = $pachube->retrieveData ( $feed, "csv" ); </code>';
echo $data;

echo "</li><hr>";
echo "<li><b>Retrieve raw feed data as JSON, using feed ID only: </b>";
$feed = 480;
$data = $pachube->getFeedData ( $feed, "json" );
echo '<code>$data = $pachube->retrieveData ( $feed, "json" ); </code>';
echo "<br><textarea rows=\"6\" cols=\"80\">$data</textarea>";

echo "</li><hr>";
echo "<li><b>Retrieve raw feed data as EEML, using feed ID only: </b>";
$feed = 480;
$data = $pachube->getFeedData ( $feed, "xml" );
echo '<code>$data = $pachube->retrieveData ( $feed, "xml" ); </code>';
echo "<br><textarea rows=\"6\" cols=\"80\">$data</textarea>";



# *****************************************************************
#
# display a graph without loading $environment
#
# *****************************************************************


echo "</li><hr>";
echo "<li><b>Display a Pachube datastream graph without creating \$environment: <br> </b>";

$feed_id=504;
$datastream_id=1;

echo '<code>$pachube->showGraph ( $feed_id, $datastream_id );	 </code>';

$pachube->showFeedGraph ( $feed_id, $datastream_id );	


# *****************************************************************
#
# display a configured graph
#
# *****************************************************************


echo "</li><hr>";
echo "<li><b>Display a <i>configured</i> Pachube datastream graph without creating \$environment: <br> </b>";

$feed_id=504;
$datastream_id=1;

// parameters are showGraph ( $feed_id, $datastream_id, $width, $height, $colour, $label[true/false], $grid[true/false], $title, $legend, $stroke);	

echo '<code>$pachube->showGraph ( $feed_id, $datastream_id, 500, 300, "00FF00", true, true, "My configured graph title", "My datastream units", 6 );	 </code>';

$pachube->showFeedGraph ( $feed_id, $datastream_id, 500, 300, "00FF00", true, true, "My configured graph title", "My datastream units", 6 );	


# *****************************************************************
#
# create a new Pachube feed
#
# *****************************************************************


echo "</li><hr>";
echo "<li><b>Create a new manual Pachube feed: </b>";
$title = "new feed from php library final";

echo '<code>$new_feed_id = $pachube->createFeed ( $title ); </code>';

$new_feed_id = $pachube->createFeed ( $title );	

// bad hack, but for the moment unsuccessful attempts to create simply
// return their HTTP status code, as a negative number

echo $new_feed_id;

# *****************************************************************
#
# delete a Pachube feed
#
# *****************************************************************


echo "</li><hr>";
echo "<li><b>Delete a Pachube feed (note this is set to delete the feed we just created): </b>";

echo '<code>$delete_status = $pachube->deletePachube ( $new_feed_id ); </code>';

$delete_status = $pachube->deleteFeed ( $new_feed_id );	
$pachube->_debugStatus($delete_status);





#/*
# *****************************************************************
#
# update manual feed: CSV
#
# *****************************************************************

echo "</li><hr>";
echo "<li><b>Update a manual feed with CSV: </b>";
// note that you must own the feed listed below in order to update it!
$url = "http://www.pachube.com/api/25842.csv";
$data = "1,3,5";

echo '<code>$update_status = $pachube->updatePachube ( $url, $data ); </code>';

// this next line makes the actual update attempt and returns a status code

$update_status = $pachube->updateFeedManualByURl( $url, $data );	
$pachube->_debugStatus($update_status);

# *****************************************************************
#
# update manual feed: EEML
#
# *****************************************************************

echo "</li><hr>";
echo "<li><b>Update a manual feed with EEML: </b>";
// note that you must own the feed listed below in order to update it!
$url = "http://www.pachube.com/api/25842.xml";

echo '<code>$update_status = $pachube->updatePachube ( $url, $data ); </code>';

$data = <<<END
<eeml xmlns="http://www.eeml.org/xsd/005"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.eeml.org/xsd/005 http://www.eeml.org/xsd/005/005.xsd">
    <environment>
    <title>new post</title>
        <data id="0">
            <value>48.7</value>
        </data>
    </environment>
</eeml>
END;
$update_status = $pachube->updateFeedManualByURl( $url, $data );	
$pachube->_debugStatus($update_status);


# *****************************************************************
#
# retrieve history data as an array
#
# *****************************************************************

echo "</li><hr>";
echo "<li><b>Retrieve history data as an array: </b>";
$url = "http://www.pachube.com/feeds/504/datastreams/1/history.csv";

echo '<code>$history = $pachube->retrieveHistory ( $url ); </code>';

$history = $pachube->getFeedHistory ( $url );
print_r ($history);




# *****************************************************************
#
# Retrieving lat/lon of feeds that contain a term as an array
# currently only displays first 10 ordered by 'retrieved_at'
#
# *****************************************************************


echo "</li><hr>";
echo "<li><b>Retrieve lat/lon of feeds that contain a term as an array: </b> <br>";

echo '<code>$latitude_and_longitudes = $pachube->getLatLon("current cost");	 </code>';

$latitude_and_longitudes = $pachube->getFeedsLocations("current cost");

print_r ( $latitude_and_longitudes );

echo "</li>";


?>
</ul>
</body>
</html>