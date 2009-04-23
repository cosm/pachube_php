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

$api_key = "ENTER_API_KEY";
$pachube = new Pachube($api_key);



# *****************************************************************
#
# retrieve feed data
#
# *****************************************************************

echo "<hr>";
echo "retrieving feed data as CSV: ";
$url = "http://www.pachube.com/api/504.csv";
$data = $pachube->retrieveData ( $url );
echo $data;


echo "<hr>";
echo "retrieving feed data as XML: ";
$url = "http://www.pachube.com/api/256.xml";
$data = $pachube->retrieveData ( $url );
echo "<br><textarea rows=\"5\" cols=\"80\">$data</textarea>";


echo "<hr>";
echo "retrieving feed data as CSV, using feed ID only: ";
$feed = 480;
$data = $pachube->retrieveData ( $feed, "csv" );
echo $data;


# *****************************************************************
#
# update manual feed: CSV
#
# *****************************************************************

echo "<hr>";
echo "updating a manual feed with CSV: ";
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
echo "updating a manual feed with EEML: ";
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
echo "retrieving history data as an array: ";
$url = "http://www.pachube.com/feeds/504/datastreams/1/history.csv";
$history = $pachube->retrieveHistory ( $url );
print_r ($history);


# *****************************************************************
#
# create a new Pachube feed
#
# *****************************************************************


echo "<hr>";
echo "create a new manual Pachube feed: ";
$title = "new feed from php library final";

$new_feed_id = $pachube->createFeed ( $title );	

// bad hack, but for the moment unsuccessful attempts to create simply
// return their HTTP status code, as a negative number

echo $new_feed_id;

# *****************************************************************
#
# delete a Pachube feed
#
# *****************************************************************


echo "<hr>";
echo "delete a Pachube feed: ";

$delete_status = $pachube->deletePachube ( $new_feed_id );	
$pachube->debugStatusCode($delete_status);

?>