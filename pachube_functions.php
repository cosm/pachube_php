<?php

# Pachube functions
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


// This class is used for manipulating the API of www.pachube.com, a service 
// that enables you to connect, tag and share real time sensor data from 
// objects, devices, buildings and environments around the world. 
//
// Currently only supports updating a manual feed. More API functionality to 
// be added over time.
// Requires PHP 5


class Pachube
{
		
	private $Api;
	
	private $UrlPachube = 'http://www.pachube.com/api/';
	
	function __construct ($api) 
	{
		$this->Api = $api;
	}
	 		
/********************* Methods *****************************/

// Update a Pachube manual feed

	public function updatePachube ( $feed='', $data='', $type='' )
	{ 
		if(empty($this->Api))
		{
			return 401;
		} else 
		{
			if(!empty($data) && !empty($feed) && !empty($type))
			{
				$url = $this->UrlPachube;
				$url .= "$feed.$type";
				
				$request = $this->updateRequestToPachube($url, $data);
				return $request;
				
			}else
			{
				return 777;
			}
		}
	}
		


// Actual request that makes the Pachube update

	private function updateRequestToPachube ( $url='', $data='')
	{
		$ret = -1;
		{
			if(function_exists(curl_init))
			{	
				$pachube_headers  = array("X-PachubeApiKey: $this->Api");

    			$putData = tmpfile();
				fwrite($putData, $data);
				fseek($putData, 0);
   

				$ch = curl_init();	
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $pachube_headers);
				curl_setopt($ch, CURLOPT_INFILE, $putData); 
				curl_setopt($ch, CURLOPT_INFILESIZE, strlen($data)); 
				curl_setopt($ch, CURLOPT_PUT, true);
						
				curl_exec($ch);
	
				$Headers = curl_getinfo($ch);
				fclose($putData);
				curl_close($ch);
							
				if($Headers['http_code'] == 200)
				{
					$ret = 200;
				} else
				{
				
					switch ($Headers['http_code']) {
   						case 401:
							$ret = 401;
							// echo $ret;
				        break;

   						case 404:
							$ret = 404;
							// echo $ret;
				        break;

						default:
							$ret = $Headers['http_code'];
						break;
							
					}
				}				
			} 
			else
			{
				$ret = 999;
			}				
		}
		
	return $ret;
   } 
}
?>

