<?php
/**
 * Pachube API class
 * Version 0.2.1 (June 2011)
 * Requirements: PHP5, cURL, API v.1.0
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * erchantability or fitness for a particular purpose. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * Online: http://www.gnu.org/licenses/gpl.txt
 */

class PachubeAPI
{
	private $Api;
	private $Pachube;
	private $Pachube_headers;
	
	/**
	 * Constructor
	 */
	function __construct($api) 
	{
		$this->Api = $api;
		$this->Pachube = "www.pachube.com";
		$this->Pachube_headers  = array("X-PachubeApiKey: $this->Api");
	}
	
	/**
	 * Update a Pachube manual feed by URL
	 * @param string feed URL
	 * @param string data
	 * @return int http response code
	 */
	public function updateFeedManualByURl($url='', $data='')
	{ 
		if(empty($this->Api))
		{
			return 401;
		}
		else 
		{
			if(!empty($url))
			{
				return $this->_putRequest($url, $data);
			}
			else
			{
				return 404;
			}
		}
	}
	
	/**
	 * Update a Pachube manual feed by IDs
	 * @param int feed ID
	 * @param int datastream ID
	 * @param float value
	 * @return int http response code
	 */
	public function updateFeedManualByIDs($feed='', $datastream='', $value='')
	{ 
		if(empty($this->Api))
		{
			return 401;
		}
		else 
		{
			if(is_numeric($feed) && is_numeric($datastream))
			{
				if(is_numeric($value))
				{
					$url = "http://$this->Pachube/api/feeds/$feed/datastreams/$datastream.csv";
					return $this->putRequestToPachube($url, $value);
				}
				else
				{
					return 418;
				}
			}
			else
			{
				return 404;
			}
		}
	}
	
	/**
	 * Get data from Pachube feed.
	 * @param string feed URL
	 * @param string feed type
	 * @return string feed data
	 */
	public function getFeedData($url='', $type='')
	{ 
		if(empty($this->Api))
		{
			return 401;
		}
		else 
		{
			if(!empty($url))
			{
				if($type != '')
				{
					$url = "http://$this->Pachube/api/feeds/$url.$type";
					return $this->_getRequest($url);
				}
				else
				{
					return $this->_getRequest($url);
				}
			}
			else
			{
				return 404;
			}
		}
	}
	
	/**
	 * Get archive data from Pachube feed.
	 * @param string feed URL
	 * @return array of objects, feed archive
	 */
	public function getFeedArchive($url='')
	{
		if(empty($this->Api))
		{
			return 401;
		} 
		else 
		{
			if(!empty($url))
			{
				$return = array();
				$data = $this->_curl($url);
				$data = explode("\n", $data);
				foreach($data as $record)
				{
					$output = new StdClass;
					$record = explode(",", $record);
					$date = explode("T", $record[0]);
					$output->date = $date[0];
					$output->time = substr($date[1],0,-1);
					$output->value = $record[1];
					$return[] = $output;
				}
				return $return;
			}
			else
			{
				return 404;
			}
		}
	}
	
	/**
	 * Get history of Pachube feed.
	 * @param string feed URL
	 * @return array of values
	 */
	public function getFeedHistory($url='')
	{ 
		if(!empty($url))
		{
			if($data = $this->_curl($url))
			{
				return explode(",",$data);
			}
			else
			{
				return 500;
			}
		}
		else
		{
			return 404;
		}		
	}
	
	/**
	 * Get environment information
	 * @param int feed ID
	 * @return associative array
	 */
	public function getEnvironment($feed_id)
	{	
		$data = json_decode($this->getFeedData($feed_id), true);		
		$return_data = (!is_array($data)) ? json_decode("{\"location\":{}\"datastreams\":[{}]}") : $data;
		return $return_data;
	}
		
	/**
	 * Create a feed on Pachube.
	 * @param string feed title
	 * @return feed ID
	 */
	public function createFeed($title='')
	{ 
		if(empty($this->Api))
		{
			return 401;
		}
		else 
		{
			if(!empty($title))
			{			
				$eeml = "<eeml xmlns=\"http://www.eeml.org/xsd/005\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.eeml.org/xsd/005 http://www.eeml.org/xsd/005/005.xsd\"><environment><title>$title</title></environment></eeml>";

				if(function_exists('curl_init'))
				{	
					$url = "http://$this->Pachube/api.xml";				

					$ch = curl_init();	
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HEADER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $eeml);

					$return = curl_exec($ch);
					$headers = curl_getinfo($ch);
					curl_close($ch);

					$status = $headers['http_code'];				

					if ($status != 201)
					{
						return $status;
					}
					else
					{
						return trim($this->_stringBetween($return,"Location: http://$this->Pachube/api/feeds/","\n"));			
					}																
				} 
				else
				{
					return 500;
				}
			}
			else
			{
				return 418;
			}
		}
	}
	
	/**
	 * Delete a feed on Pachube.
	 * @param int feed ID
	 * @return int http response code
	 */
	public function deleteFeed ($feed_id='')
	{ 
		if(empty($this->Api))
		{
			return 401;
		}
		else 
		{
			if(is_numeric($feed_id))
			{
				$url = "http://$this->Pachube/api/feeds/".$feed_id;

				if(function_exists('curl_init'))
				{	
					$ch = curl_init();	
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_exec($ch);
					$headers = curl_getinfo($ch);
					curl_close($ch);

					return $headers['http_code'];
				} 
				else
				{
					return 500;
				}				
			}
			else
			{
				return 418;
			}
		}
	}

	/**
	 * Get coordinates of feeds, which match query
	 * @param string query
	 * @return array of arrays with coordinates
	 */
	public function getFeedsLocations($query='') 
	{
		if (!empty($query))
		{
			$search = urlencode($query);
			$url = "http://$this->Pachube/api/feeds.json?order=created_at&q=$query";
			
			if($data = $this->_curl($url, true))
			{
				$json_data = json_decode($data, true);
			}
			else
			{
				return 500;
			}
			
			$reported_locations = Array();
			
			foreach($json_data["results"] as $result) 
			{									
				$location = $result["location"];
				$this_location = Array();
				$this_location["lat"] = $location["lat"];
				$this_location["lon"] = $location["lon"];
				array_push($reported_locations, $this_location);
			}
			return $reported_locations;		
		}
		else
		{
			return 418;
		}
	}

	/**
	 * Show feed graph.
	 * @param int feed ID
	 * @param int datastream ID
	 * @param int width of image
	 * @param int height of image
	 * @param string color
	 * @param bool label
	 * @param bool grid oon image
	 * @param string title of image
	 * @param string legend parameters
	 * @param int stroke
	 */
	public function showFeedGraph($feed_id='', $datastream_id='', $width='300', $height='200', $colour='FF0066', $label=true, $grid=true, $title='', $legend='', $stroke='4')
	{
		if(is_numeric($feed_id) && is_numeric($datastream_id))
		{
			$legend_param = empty($legend) ? "" : "&l=$legend";
			$grid_param = $grid ? "&g=$grid" : "";
			$label_param = $label ? "&b=$label" : "";
			$title_param = (strcmp($title,"")==0) ? "" : "&t=$title";

			echo "<img src=\"http://$this->Pachube/feeds/$feed_id/datastreams/$datastream_id/history.png?w=$width&h=$height&c=$colour$label_param$grid_param$title_param$legend_param&s=$stroke\" width=\"$width\" height=\"$height\" border=\"1\" alt=\"Powered by Pachube.com\">";		
		}
	}

	/**
	 * Show environment graph.
	 * @param array environment data
	 * @param int datastream ID
	 * @param int width of image
	 * @param int height of image
	 * @param string color
	 * @param bool label
	 * @param bool grid oon image
	 * @param string title of image
	 * @param string legend parameters
	 * @param int stroke
	 */
	public function showEnvironmentGraph($environment, $datastream_id, $width='300', $height='200', $colour='FF0066', $label=true, $grid=true, $title='', $legend='', $stroke='4')
	{	
		$feed_id = $environment['id'];
		$this->showFeedGraph($feed_id, $datastream_id, $width, $height, $colour, $label, $grid, $title, $legend, $stroke );
	}

	/**
	 * Show environment graph.
	 * @param array environment data
	 * @param int width of image
	 * @param int height of image
	 * @param string google map API key
	 */
	public function showEnvironmentMap($environment, $width, $height, $google_map_key)
	{	
		if (count($environment) != 0 && isset($environment['location']['lat']))
		{
			$width .= "px";
			$height .= "px";
			$lat = $environment['location']['lat'];
			$lon = $environment['location']['lon'];
			$gmap_text = "<div id=\"pachube_map\" style=\"width: $width; height: $height\"></div><script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=$google_map_key\" type=\"text/javascript\"></script><script type=\"text/javascript\"> function initialize() { if (GBrowserIsCompatible()) { var map = new GMap2(document.getElementById(\"pachube_map\")); map.setCenter(new GLatLng($lat, $lon), 13); map.setUIToDefault(); var point = new GLatLng($lat, $lon); map.addOverlay(new GMarker(point)); }}initialize();</script>";
			echo $gmap_text;
		}
		else
		{
			$this->_debugStatus(418);
		}
	}
	
	/**
	 * Create GET request to Pachube (wrapper)
	 * @param string url
	 * @return response
	 */
	private function _getRequest($url)
	{		
		if(function_exists('curl_init'))
		{
			return $this->_curl($url,true);
		}
		elseif(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		{
			return $this->_get($url);		
		}
		else
		{
			return 500;
		}
	}

	/**
	 * Create PUT request to Pachube (wrapper)
	 * @param string url
	 * @param string data
	 * @return response
	 */
	private function _putRequest($url, $data)
	{	
		if(function_exists('curl_init'))
		{
			$putData = tmpfile();
			fwrite($putData, $data);
			fseek($putData, 0);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
			curl_setopt($ch, CURLOPT_INFILE, $putData);
			curl_setopt($ch, CURLOPT_INFILESIZE, strlen($data));
			curl_setopt($ch, CURLOPT_PUT, true);
			curl_exec($ch);
			$headers = curl_getinfo($ch);
			fclose($putData);
			curl_close($ch);

			return $headers['http_code'];
		}
		elseif(function_exists('file_put_contents') && ini_get('allow_url_fopen'))
		{
			return $this->_put($url,$data);
		}
		else
		{
			return 500;
		}
	}

	/**
	 * cURL main function
	 * @param string url
	 * @param bool authentication
	 * @return response
	 */
	private function _curl($url, $auth=false)
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($auth)
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Pachube_headers);
			}
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * GET requests to Pachube
	 * @param string url
	 * @return response
	 */
	private function _get($url)
	{
		// Create a stream
		$opts['http']['method'] = "GET";
		$opts['http']['header'] = "X-PachubeApiKey: ".$this->Api."\r\n";
		$context = stream_context_create($opts);
		// Open the file using the HTTP headers set above
		return file_get_contents($url, false, $context);
	}

	/**
	 * PUT requests to Pachube
	 * @param string url
	 * @param string data
	 * @return response
	 */
	private function _put($url,$data)
	{	
		// Create a stream
		$opts['http']['method'] = "PUT";
		$opts['http']['header'] = "X-PachubeApiKey: ".$this->Api."\r\n";
		$opts['http']['header'] .= "Content-Length: " . strlen($data) . "\r\n";
		$opts['http']['content'] = $data;
		$context = stream_context_create($opts);
		// Open the file using the HTTP headers set above
		return file_get_contents($url, false, $context);
	}
	
	/**
	 * Find string between to strings
	 * @param string content where to search
	 * @param string start point
	 * @param string end point
	 * @return response
	 */
	private function _stringBetween($content, $start, $end)
	{
		$r = explode($start, $content);
		if (isset($r[1]))
		{
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return '';
	}

	/**
	 * Print debug status
	 * @param int status code
	 */
	public function _debugStatus($status_code)
	{
		switch ($status_code)
		{			
			case 200:
				$msg = "Pachube feed successfully updated";	
				break;
			case 401:
				$msg = "Pachube API key was incorrect";
				break;
			case 404:
				$msg = "Feed ID or some other parameter does not exist";
				break;
			case 422:
				$msg = "Unprocessable Entity, semantic errors (CSV instead of XML?)";
				break;
			case 418:
				$msg = "Error in feed ID, data type or some other data";
				break;
			case 500:
				$msg = "cURL library not installed or some other internal error occured";
				break;	
			default:
				$msg = "Status code not recognised: ".$status_code;
				break;
		}
		echo $msg;		
	}
}
?>