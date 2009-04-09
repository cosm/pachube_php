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

// this is a basic example for updating a Pachube manual feed 
// requires PHP 5


require_once( 'pachube_functions.php' );

$api_key = "ENTER_API_KEY";
$feed = 1666;
$data = "3.4,66,7";
$data_type = "csv"; // can be 'csv' or'xml' 

// creates a new Pachube object with the given API key

$pachube = new Pachube($api_key);

// this next line makes the actual update attempt and returns a status code

$update_status = $pachube->updatePachube ( $feed, $data, $data_type );	

$error = "";

// status code returns 200 if successful
	
switch ($update_status){
	
	case 200:
		echo "Pachube feed ".$feed." successfully update with the following data: ".$data;	
		break;

	case 401:
		$error.= "Pachube API key was incorrect";
		break;

	case 404:
		$error.= "Feed ID does not exist";
		break;

	case 777:
		$error.= "Error in feed ID, data type or data";
		break;

	case 999:
		$error.= "curl library not installed";
		break;

}

echo $error;

?>