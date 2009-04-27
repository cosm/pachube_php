<html>
<head>
</head>
<body>

<?php	

# Pachube basic example
#
#**********************************************************************
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# ( at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Online: http://www.gnu.org/licenses/gpl.txt

# *****************************************************************

// Used for manipulating the API of www.pachube.com, a service 
// that enables you to connect, tag and share real time sensor data from 
// objects, devices, buildings and environments around the world. 
//
// This is a basic example for updating a Pachube manual feed.
// 
// Requires PHP 5


require_once( 'pachube_functions.php' );

$api_key = "ENTER_YOUR_API_KEY";
$pachube = new Pachube($api_key);


#/*
# *****************************************************************
#
# retrieve Pachube feed data
#
# *****************************************************************

echo "<hr>";
echo "<b>retrieving feed data as CSV: </b>";
$url = "http://www.pachube.com/api/504.csv";
$data = $pachube->retrieveData ( $url );
echo $data;

echo "<hr>";
echo "<b>retrieving feed data as XML: </b>";
$url = "http://www.pachube.com/api/256.xml";
$data = $pachube->retrieveData ( $url );
echo "<br><textarea rows=\"6\" cols=\"80\">$data</textarea>";

echo "<hr>";
echo "<b>retrieving feed data as CSV, using feed ID only: </b>";
$feed = 480;
$data = $pachube->retrieveData ( $feed, "csv" );
echo $data;

echo "<hr>";
echo "<b>retrieving feed data as JSON, using feed ID only: </b>";
$feed = 480;
$data = $pachube->retrieveData ( $feed, "json" );
echo $data;

echo "<hr>";
echo "<b>retrieving feed data as EEML, using feed ID only: </b>";
$feed = 480;
$data = $pachube->retrieveData ( $feed, "xml" );
echo "<br><textarea rows=\"6\" cols=\"80\">$data</textarea>";


#*/

# *****************************************************************
#
# working with Environments (returns an associative array)
#
# *****************************************************************

echo "<hr>";
echo "<b>working with Environments: </b><br>";

$feed_id = 504;
$environment = $pachube->environment( $feed_id );

echo "description: ".$environment['description']."<br>";
echo "status: ".$environment['status']."<br>";
echo "location name: ".$environment['location']['name']."<br>";
echo "latitude: ".$environment['location']['lat']."<br>";
echo "longitude: ".$environment['location']['lon']."<br>";
echo "exposure: ".$environment['location']['exposure']."<br>";
echo "number of datastreams: ".count($environment['datastreams'])."<br>";
echo "value of datastream 2: ".$environment['datastreams']['2']['value']['current_value']."<br>";

$pachube->showEnvironmentGraph($environment,1);
echo "<br>";
$pachube->showEnvironmentGraph($environment,2, 700, 250, "0000FF", false, false, "My configured graph title", "My datastream units", 6);
echo "<br>";

// the following requires you to have a Google Map API key for your domain available here: http://code.google.com/apis/maps/

$pachube->showEnvironmentMap($environment, 500, 200, "ABQIAAAAYGdShHJUqUUqCZujCgqoyxRhf0yX7jCDZEW8LvcORLdH4560mRQtTT3Vx6wORcHDcMrtNf9XNlmO0w");


#/*
# *****************************************************************
#
# update manual feed: CSV
#
# *****************************************************************

echo "<hr>";
echo "<b>updating a manual feed with CSV: </b>";
// note that you must own the feed listed below in order to update it!
$url = "http://www.pachube.com/api/1666.csv";
$data = "1,3,5";

// this next line makes the actual update attempt and returns a status code

$update_status = $pachube->updatePachube ( $url, $data );	
$pachube->debugStatusCode($update_status);

# *****************************************************************
#
# update manual feed: EEML
#
# *****************************************************************

echo "<hr>";
echo "<b>updating a manual feed with EEML: </b>";
// note that you must own the feed listed below in order to update it!
$url = "http://www.pachube.com/api/1666.xml";
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
$update_status = $pachube->updatePachube ( $url, $data );	
$pachube->debugStatusCode($update_status);


# *****************************************************************
#
# retrieve history data as an array
#
# *****************************************************************

echo "<hr>";
echo "<b>retrieving history data as an array: </b>";
$url = "http://www.pachube.com/feeds/504/datastreams/1/history.csv";
$history = $pachube->retrieveHistory ( $url );
print_r ($history);


# *****************************************************************
#
# create a new Pachube feed
#
# *****************************************************************


echo "<hr>";
echo "<b>create a new manual Pachube feed: </b>";
$title = "new feed from php library final";

//$new_feed_id = $pachube->createFeed ( $title );	

// bad hack, but for the moment unsuccessful attempts to create simply
// return their HTTP status code, as a negative number

echo $new_feed_id;

# *****************************************************************
#
# delete a Pachube feed
#
# *****************************************************************


echo "<hr>";
echo "<b>delete a Pachube feed (note this is set to delete the feed we just created): </b>";

//$delete_status = $pachube->deletePachube ( $new_feed_id );	
//$pachube->debugStatusCode($delete_status);


# *****************************************************************
#
# display a graph
#
# *****************************************************************


echo "<hr>";
echo "<b>Display a Pachube datastream graph: <br> </b>";

$feed_id=504;
$datastream_id=1;

$pachube->showGraph ( $feed_id, $datastream_id );	


# *****************************************************************
#
# display a configured graph
#
# *****************************************************************


echo "<hr>";
echo "<b>Display a <i>configured</i> Pachube datastream graph: <br> </b>";

$feed_id=504;
$datastream_id=1;

// parameters are showGraph ( $feed_id, $datastream_id, $width, $height, $colour, $label[true/false], $grid[true/false], $title, $legend, $stroke);	

$pachube->showGraph ( $feed_id, $datastream_id, 500, 300, "00FF00", true, true, "My configured graph title", "My datastream units", 6 );	



# *****************************************************************
#
# Retrieving lat/lon of feeds that contain a term as an array
# currently only displays first 10 ordered by 'retrieved_at'
#
# *****************************************************************


echo "<hr>";
echo "<b>retrieving lat/lon of feeds that contain a term as an array: </b> <br>";

$latitude_and_longitudes = $pachube->getLatLon("current cost");

print_r ( $latitude_and_longitudes );

#*/
?>

</body>
</html>