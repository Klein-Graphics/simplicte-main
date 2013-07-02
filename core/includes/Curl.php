<?php

/**
 * Author: Vincent Faliès, <vincent.falies@kit-digital.com>
 * This file is a Curl class.
 * http://developers.kewego.fr/en/tutorials/the-curl-class.html
 * 
 * Note: 
 * To use proxy for all curl calls, you must defined following constants :
 * PROXY_HOST, PROXY_USERNAME, PROXY_PWD, PROXY_PORT
 *
 * Copyright (c) 2011 Kewego SAS.
 *
 * Released under the Kewego License:
 * http://developers.kewego.fr/license.html
 */
class Curl
{
	private $ssl_verify_peer;	// certificat verification
	private $timeout;			// curl timeout
	private $last_error;

	/**
	 * 
	 * Constructor
	 */
	public function __construct()
	{
		$this->ssl_verify_peer	= false; 	// No certificat verification
		$this->timeout			= -1;		// Curl timeout disable
		$this->last_error		= '';
	}	
	
	/**
	 * 
	 * Curl call method
	 * @param string $api_url url of the call 
	 * @param array $data_array data of the call
	 * @param array $info information regarding a specific transfer
	 * @param string $method method of the call (get/post/post_file/delete/raw)
	 */
	private function Call($api_url, $data_array='', &$info='', $method='post')
	{
		// Check if curl extension is loaded
		if (extension_loaded('curl'))
		{
			$parsed_url = parse_url($api_url);

			// parse url for params
			$tmp_url = '';
			isset($parsed_url['scheme']) ? $tmp_url .= $tmp_url = $parsed_url['scheme'].'://' : '';
			isset($parsed_url['host']) ? $tmp_url .= $parsed_url['host'] : '';
			isset($parsed_url['port']) ? $tmp_url .= ':'.$parsed_url['port'] : '';
			isset($parsed_url['path']) ? $tmp_url .= $parsed_url['path'] : '';
			
			$ch         = curl_init();

			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);						
			
			// Check if proxy informations are defined
			if (defined('PROXY_HOST') && defined('PROXY_USERNAME') && defined('PROXY_PWD') && defined('PROXY_PORT'))
			{
				curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
				curl_setopt($ch, CURLOPT_PROXY, PROXY_HOST);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, PROXY_USERNAME.':'.PROXY_PWD);
				curl_setopt($ch, CURLOPT_PROXYPORT, PROXY_PORT);
			}
							
			// Check ssl peer's certicate
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify_peer);
			if (!$this->ssl_verify_peer)
			{
				curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);	
			}			 
				
			// Check timeout
			if ($this->timeout != -1)			
			{
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
			}
			
			// Getting GET Params
			$post_data = array();
			if (isset($parsed_url['query']))
			{
				$query_arg = explode('&',$parsed_url['query']);
				foreach($query_arg as $key => $arg)
				{
					$detail_arg = explode('=', $arg);
					$post_data[$detail_arg[0]] = $detail_arg[1];
				}
			}
			// Getting data params
			if ($data_array != '')
			{
				foreach($data_array as $data_key => $data)
				{
					$post_data[$data_key] = $data;
				}
			}

			switch ($method)
			{
				case 'post_file': // Curl call with POST method with a file
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
					curl_setopt($ch, CURLOPT_URL, $api_url);
					break;
				case 'post': // Curl call with POST method
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
					curl_setopt($ch, CURLOPT_URL, $api_url);
					break;
				case 'get': // Curl call with GET method
					if (isset($data_array) && !empty($data_array))
					{
						// Add params to url
						if (strpos($api_url, '?') !== false)
						{
							$api_url .= '&';
						}
						else
						{
							$api_url .= '?';
						}
						curl_setopt($ch, CURLOPT_URL, $api_url.http_build_query($post_data));
					}
					else
					{
						curl_setopt($ch, CURLOPT_URL, $api_url);
					}
					break;
				case 'delete': // Curl call with DELETE method
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
					curl_setopt($ch, CURLOPT_URL, $api_url);
					break;
				case 'raw': // Curl call with RAW method
					curl_setopt($ch, CURLOPT_URL, $api_url);
					break;
				default:
					return false;
			}

			$res				= curl_exec($ch); 		// Execute curl call
			$info 				= curl_getinfo($ch); 	// Getting information from curl call
				
			$this->last_error 	= curl_error($ch); 		// Getting error of curl call
				
			curl_close($ch); 							// Close curl connection
			return $res;
		}
		else
		{		    
			return false; // Curl extension is not loaded
		}
	}
	
	/**
	 * 
	 * Curl call with GET method
	 * @param string $url 
	 * @param array $params 
	 * @param mixed $info information about the last transfer
	 */
	public function get($url, $params='', &$info='')
	{
		return $this->Call($url, $params, $info, 'get');		
	} 

	/**
	 * 
	 * Curl call with POST method
	 * @param string $url 
	 * @param array $params 
	 * @param mixed $info information about the last transfer
	 */
	public function post($url, $params='', &$info='')
	{
		return $this->Call($url, $params, $info, 'post');		
	} 	
	
	/**
	 * 
	 * Curl call with POST method for file
	 * @param string $url 
	 * @param array $params Value content an '@' for file upload 
	 * @param mixed $info information about the last transfer
	 */
	public function postFile($url, $params='', &$info='')
	{
		return $this->Call($url, $params, $info, 'post_file');
	}
	
	/**
	 * 
	 * Curl call with DELETE method
	 * @param string $url 
	 * @param array $params 
	 * @param mixed $info information about the last transfer
	 */
	public function delete($url, $params='', &$info='')
	{
		return $this->Call($url, $params, $info, 'delete');
	}

	/**
	 * 
	 * Curl call with RAW method
	 * @param string $url 
	 * @param array $params 
	 * @param mixed $info information about the last transfer
	 */
	public function raw($url, &$info='')
	{
		return $this->Call($url, '', $info, 'raw');
	}
	
	/**
	 * 
	 * Setting SSL peer's certificate verification
	 * @param boolean $ssl_verify_peer
	 */
	public function setSSLVerifyPeer($ssl_verify_peer)
	{
		$this->ssl_verify_peer = $ssl_verify_peer;
	}

	/**
	 * 
	 * Setting timeout of call
	 * @param integer $timeout -1 to disable
	 */
	public function setTimeOut($timeout)
	{
		$this->timeout = $timeout;
	}
	
	/**
	 * 
	 * Getting last error of curl call
	 */
	public function getLastError()
	{
		return $this->last_error;
	}
}

?>
