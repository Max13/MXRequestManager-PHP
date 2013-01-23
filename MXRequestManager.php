<?php
/**
 * @brief		MXRequestManager
 *
 * @details		REST Request Manager by Max13
 *
 * @version		0.2
 * @author		Adnan "Max13" RIHAN <adnan@rihan.fr>
 * @link		http://rihan.fr/
 * @copyright	http://creativecommons.org/licenses/by-sa/3.0/	CC-by-sa 3.0
 *
 * LICENSE: This source file is subject to the "Attribution-ShareAlike 3.0 Unported"
 * of the Creative Commons license, that is available through the world-wide-web
 * at the following URI: http://creativecommons.org/licenses/by-sa/3.0/.
 * If you did not receive a copy of this Creative Commons License and are unable
 * to obtain it through the web, please send a note to:
 * "Creative Commons, 171 Second Street, Suite 300,
 * San Francisco, California 94105, USA" so we can mail you a copy immediately.
 */

/**
 * Internal Errors
 * -2  = Failed on request execution
 * -10 = Can't change User-Agent
 * -11 = Can't set POSTFIELDS
 * -12 = Can't set URL
 * -13 = Can't set COOKIES
 * -14 = Can't separate headers and body from response (Bad response)
 * -15 = Can't find headers (Bad headers)
 * -16 = Can't set the request to HEAD
 * -17 = Can't set the request to GET
 * -18 = Can't set the request to PUT
 * -19 = Can't set the request to POST
 * -20 = Can't set the request to DELETE
 * -21 = Can't set the request to CUSTOM
 */

class	MXRequestManager
{
	/**
	 * MXRequestManager Name
	 */
	const NAME = 'MXRequestManager';

	/**
	 * MXRequestManager Version
	 */
	const VERSION = '0.2';

	/**
	 * MXRequestManager internal info
	 */
	static private $internalInfo = array();

	/**
	 * cURL Handle Resource
	 *
	 * @var cURL resource
	 */
	protected $m_curlResource;

	/**
	 * Base API URL
	 *
	 * @var Base (Root) API URL
	 */
	protected $m_baseApiUrl;

	/**
	 * Auth API User
	 *
	 * @var Auth User
	 */
	protected $m_authApiUser;

	/**
	 * Auth API Pass
	 *
	 * @var Auth Pass
	 */
	protected $m_authApiPass;

	/**
	 * Current Request Cookies
	 *
	 * @var Associative Array of Current Request Cookies
	 */
	protected $m_cookies = array();

	/**
	 * Last Error Number
	 *
	 * @var Last Error Number
	 */
	protected $m_errno = 0;

	/**
	 * Raw Response
	 *
	 * @var Raw Response
	 */
	protected $m_rawResponse;

	/**
	 * Response
	 *
	 * @var Response array. Contains ['header'] and ['body']
	 */
	protected $m_response = array();

	// -------------------- //

	/**
	 * Constructor
	 *
	 * @param[in]	$baseApiUrl	Base URL of the API (Form: http[s]://base.api.url/)
	 * @param[in]	$apiUser	Auth User of the API
	 * @param[in]	$apiPass	Auth Pass of the API
	 */
	function __construct($baseApiUrl = NULL, $apiUser = NULL, $apiPass = NULL)
	{
		$curl_version = curl_version();
		$this->m_curlResource = NULL;
		$this->m_baseApiUrl = $baseApiUrl;
		$this->m_authApiUser = $apiUser;
		$this->m_authApiPass = $apiPass;
		$this->m_cookies = array();
		// $this->m_rawResponse = NULL; // Moved to processRequest()
		// $this->m_response = array(); // Moved to processRequest()
		self::$internalInfo['curl'] = curl_version();

		if (!($this->m_curlResource = curl_init($this->m_baseApiUrl)))
			die($this->lastCurlError());

		$this->setUserAgent();

		if (!curl_setopt_array($this->m_curlResource, array(
			CURLOPT_AUTOREFERER			=>	TRUE,
			CURLOPT_HEADER				=>	TRUE,
			CURLINFO_HEADER_OUT			=>	TRUE,
			CURLOPT_RETURNTRANSFER		=>	TRUE,
			CURLOPT_UNRESTRICTED_AUTH	=>	TRUE,
			CURLOPT_CONNECTTIMEOUT		=>	15,
			CURLOPT_HTTPAUTH			=>	CURLAUTH_ANY,
			CURLOPT_TIMEOUT				=>	15,
			CURLOPT_ENCODING			=>	'',
			CURLOPT_USERPWD				=>	"$apiUser:$apiPass")))
			die($this->lastCurlError());
	}

	/**
	 * Destructor
	 */
	function __destruct()
	{
		curl_close($this->m_curlResource);
	}

	// -------------------- //

	/**
	 * cURL Resource
	 *
	 * Get cURL Resource, for debugging
	 *
	 * @return	The internal cURL resource
	 */
	public function curlResource()
	{
		return ($this->m_curlResource);
	}

	/**
	 * Base API URL
	 *
	 * Get Base API URL
	 *
	 * @return	The internal Base API URL (Root)
	 */
	public function baseApiUrl()
	{
		return ($this->m_baseApiUrl);
	}

	/**
	 * Set Base API URL
	 *
	 * @param[in]	$apiUrl	The internal Base API URL (Root)
	 */
	public function setBaseApiUrl($apiUrl)
	{
		$this->m_baseApiUrl = $apiUrl;
	}

	/**
	 * Auth API User
	 *
	 * Get Auth API User
	 *
	 * @return	The internal Auth API User
	 */
	public function authApiUser()
	{
		return ($this->m_authApiUser);
	}

	/**
	 * Set Auth API User
	 *
	 * @param[in]	$authApiUser	The internal Auth API User
	 */
	public function setAuthApiUser($authApiUser)
	{
		$this->m_authApiUser = $authApiUser;
	}

	/**
	 * Auth API Pass
	 *
	 * Get Auth API Password
	 *
	 * @return	The internal Auth API Password
	 */
	public function authApiPass()
	{
		return ($this->m_authApiPass);
	}

	/**
	 * Set Auth API Pass
	 *
	 * @param[in]	The internal Auth API Password
	 */
	public function setAuthApiPass($authApiPass)
	{
		$this->m_authApiPass = $authApiPass;
	}

	/**
	 * User Agent
	 *
	 * Get User-Agent
	 *
	 * @return	String containing the current User-Agent
	 */
	public function userAgent()
	{
		return (self::$internalInfo['user_agent']);
	}

	/**
	 * Set User-Agent
	 *
	 * @param[in]	$userAgent=NULL	Sets the User-Agent. NULL for default.
	 */
	public function setUserAgent($userAgent = NULL)
	{
		if (is_null($userAgent))
			self::$internalInfo['user_agent'] = self::NAME.'/'.self::VERSION.' PHP/'.PHP_VERSION.' cURL/'.self::$internalInfo['curl']['version'];
		else
			self::$internalInfo['user_agent'] = $userAgent;

		if (!curl_setopt($this->m_curlResource, CURLOPT_USERAGENT, self::$internalInfo['user_agent']))
			$this->m_errno = -10;
	}

	/**
	 * Cookies
	 *
	 * Get current set cookies line
	 *
	 * @param	$toArray=FALSE	If you want it as an array or "raw"
	 *
	 * @return	Array or string of current cookies line
	 */
	public function cookies($toArray = FALSE)
	{
		if ($toArray || empty($this->m_cookies))
			return ($this->m_cookies);

		$cookies = '';
		foreach ($this->m_cookies as $key => $val)
			$cookies .= ($cookies == '') ? "$key=$val" : "; $key=$val";

		return ($cookies);
	}

	/**
	 * Set Cookies
	 *
	 * Set current cookies line
	 *
	 * @param[in]	$cookies	Associative Array or normal string of cookies (<key1=val1>[; key2=val2...])
	 */
	public function setCookies($cookies)
	{
		if (empty($cookies))
			$this->m_cookies = array();
		elseif (is_array($cookies))
			$this->m_cookies = $cookies;
		elseif (is_string($cookies))
		{
			$cookiesGroups = explode('; ', $cookies);
			$cGroupsCount = count($cookiesGroups);
			for ($i=0;$i<$cGroupsCount;$i++)
			{
				$cookiesGroup = explode('=', $cookiesGroups[$i]);
				if (count($cookiesGroup) != 2)
					break;
				$this->m_cookies[$cookiesGroup[0]] = $cookiesGroup[1];
			}
		}
	}

	/**
	 * Errno
	 *
	 * Get the last internal error number
	 *
	 * @return	Integer representing the error
	 */
	public function errno()
	{
		return ($this->m_errno);
	}

	/**
	 * Set Errno
	 *
	 * Set Errno and ALWAYS return FALSE
	 *
	 * @param	$errno	The error number to set
	 *
	 * @return	FALSE
	 */
	protected function setErrno($errno)
	{
		$this->m_errno = $errno;
		return (FALSE);
	}

	/**
	 * Raw Response
	 *
	 * Get Last Raw Response
	 *
	 * @return	Raw Response (Header + "\r\n\r\n" + Body)
	 */
	public function rawResponse()
	{
		return ($this->m_rawResponse);
	}

	/**
	 * Response
	 *
	 * Get Last Response array or precise member
	 * No parameters list. Each argument get a dimension.
	 * For example: (1, 2) means: $var[1][2].
	 *
	 * @return	Response Array
	 * @return	Response String of a dimension
	 * @return	NULL if the dimention dosn't exist
	 */
	public function response()
	{
		if (func_num_args() == 0)
			return ($this->m_response);

		$argc = func_num_args();
		$argv = func_get_args();
		$arg = &$this->m_response;
		for ($i=0;$i<$argc;$i++)
		{
			if (array_key_exists($argv[$i], $arg))
				$arg = &$arg[$argv[$i]];
			else
				return (NULL);
		}

		return ($arg);
	}

	// -------------------- //

	/**
	 * Get Global Info
	 *
	 * Get Global or Specified info about current curl request
	 *
	 * @param[in]	$option=NULL	NULL for array of info, of precise the index
	 *
	 * @return	Array of global info or precise info string
	 */
	public function globalInfo($option = NULL)
	{
		$globalInfo = curl_getinfo($this->m_curlResource);
		return (empty($option) ? $globalInfo : $globalInfo[$option]);
	}

	/**
	 * Set Custom Option
	 *
	 * Set Custom cURL option
	 *
	 * @param[in]	$key	Constant Key of the option, or Array
	 * @param[in]	$val	Value of the option. Default NULL if $key is array
	 * @return		boolean
	 */
	public function setCustomOption($key, $val = NULL)
	{
		if (empty($key))
			return (TRUE);

		return (is_array($key) ? curl_setopt_array($this->m_curlResource, $key) : curl_setopt($this->m_curlResource, $key, $val));
	}

	/**
	 * Set SSL Checks
	 *
	 * Enable or Disable SSL certificate verifications/validity
	 * Clearly: Accept or Reject invalid certificates
	 *
	 * @param[in]	$enabled	FALSE to disable checks and accept invalid certificates. TRUE is Default.
	 * @return		boolean
	 */
	public function setSslChecks($enabled = TRUE)
	{
		return ($this->setCustomOption(CURLOPT_SSL_VERIFYPEER, FALSE));
	}

	/**
	 * Set Headers
	 *
	 * Set custom headers
	 *
	 * @param[in]	$headers	Associative array of headers.
	 * @return		boolean
	 */
	public function setHeaders($headers)
	{
		$head = array();
		foreach ($headers as $key => $val)
			$head[] = "$key: $val";

		return ($this->setCustomOption(CURLOPT_HTTPHEADER, $head));
	}

	/**
	 * Set Debug mode
	 *
	 * Set the debug mode. When enabled, it prints every PHP warning/errors
	 *
	 * @param[in]	$enabled	TRUE to enable.
	 */
	public function setDebugMode($enabled)
	{
		if ($enabled)
		{
			ini_set('error_reporting', 2147483647);
			ini_set('display_errors', 'stdout');
		}
	}

	/**
	 * Get Last cURL Error
	 */
	public function lastCurlError()
	{
		return ('Error '.curl_errno($this->m_curlResource).': '.curl_error($this->m_curlResource));
	}

	// /**
	//  * Get Last Error
	//  */
	// public function lastError()
	// {
	// 	return ('Error '.curl_errno($this->m_curlResource).': '.curl_error($this->m_curlResource));
	// }

	/**
	 * To Query String
	 *
	 * Returns a transformed array of parameters to a query string.
	 *
	 * @param[in]	$paramsArray	An associative array of parameters
	 */
	public function toQueryString($paramsArray = NULL, $raw = FALSE)
	{
		if (empty($paramsArray))
			return (NULL);
		return((($raw) ? '' : '?').http_build_query($paramsArray));
	}

	// -------------------- //

	/**
	 * Process Request
	 *
	 * Must be called after having set the cURL Options.
	 * Can be called with the API Resource.
	 * Can be called with or without the "Request Parameters" in the parameters.
	 * Will set the cookies, process the request, process the response
	 * And returns.
	 *
	 * @param[in]	$apiRes=NULL		The API Resource
	 * @param[in]	$parameters=NULL	The request parameters
	 * @param[in]	$preventExec=FALSE	TRUE to prevent the request execution (to execute manually)
	 * @return		BOOL representing the processing status. TRUE is everything is OK
	 */
	protected function processRequest($apiRes = NULL, $parameters = NULL, $preventExec = TRUE)
	{
		$this->m_rawResponse = NULL;
		$this->m_response = array();

		// Sets the resource and the parameters
		$currentApiURL = $this->m_baseApiUrl.$apiRes;
		$params = $this->toQueryString($parameters, TRUE);

		$backtrace = (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION >= 4) ? debug_backtrace(0, 2) : debug_backtrace();
		if ($backtrace[1]['function'] == 'post')
		{
			if (!(curl_setopt($this->m_curlResource, CURLOPT_POSTFIELDS, $params)))
				return ($this->setErrno(-11));
			$params = NULL;
		}

		if (!(curl_setopt($this->m_curlResource, CURLOPT_URL, $currentApiURL.($params == NULL ? '' : "?$params"))))
			return ($this->setErrno(-12));
		// ---

		// Stringify cookies
		$cookies = NULL;
		if (!empty($this->m_cookies))
			foreach ($this->m_cookies as $key => $val)
				$cookies .= (($cookies == NULL) ? '' : '; ')."$key=$val";
		// ---

		// Makes multiple checks
		if (!(curl_setopt($this->m_curlResource, CURLOPT_COOKIE, $cookies)))
			return ($this->setErrno(-13));
		if (($this->m_rawResponse = curl_exec($this->m_curlResource)) === FALSE)
			return ($this->setErrno(-2));
		if (!is_array(($response = explode("\r\n\r\n", $this->m_rawResponse, ($backtrace[1]['function'] == 'put' ? 3 : 2)))))
			return ($this->setErrno(-14));
		// ---

		if ($backtrace[1]['function'] == 'put' && count($response) > 2)
			array_shift($response);

		// Processes the headers + response
		$this->m_response['raw_header'] = $response[0];
		$this->m_response['raw_body'] = $response[1];

		if (($headers = explode("\r\n", $response[0])) === FALSE)
			return ($this->setErrno(-15));

		// HTTP Code
		$this->m_response['headers']['Status'] = $headers[0];
		$http_res = explode(' ', $headers[0]);
		$this->m_response['headers']['Code'] = intval($http_res[1]);
		// ---

		$nHeader = count($headers);
		for ($i=1;$i<$nHeader;$i++) // 0 is HTTP Code
		{
			$headerGroup = explode(': ', $headers[$i], 2);
			$this->m_response['headers'][$headerGroup[0]] = $headerGroup[1];
		}

		if (strncmp($this->m_response['headers']['Content-Type'], 'application/json', 16) == 0)
			return (($this->m_response['body'] = json_decode($response[1])));
		// ---

		return (TRUE);
	}

	/**
	 * HEAD
	 *
	 * Launch a HEAD Request on the API
	 *
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function head($apiRes, $apiParams = NULL)
	{
		// if (empty($apiRes))
		// 	die('Error: The resource cannot be empty.');

		if (!curl_setopt_array($this->m_curlResource, array(
			CURLOPT_HTTPGET		=>	TRUE,
			CURLOPT_NOBODY		=>	TRUE)))
			return ($this->setErrno(-16));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}

	/**
	 * GET
	 *
	 * Launch a GET Request on the API
	 *
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function get($apiRes, $apiParams = NULL)
	{
		// if (empty($apiRes))
		// 	die('Error: The resource cannot be empty.');

		if (!curl_setopt($this->m_curlResource, CURLOPT_HTTPGET, TRUE))
			return ($this->setErrno(-17));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}

	/**
	 * PUT
	 *
	 * Launch a PUT Request on the API
	 *
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function put($apiRes, $apiParams = NULL)
	{
		// if (empty($apiRes))
		// 	die('Error: The resource cannot be empty.');

		if (!curl_setopt_array($this->m_curlResource, array(
			CURLOPT_PUT			=> TRUE,
			CURLOPT_INFILESIZE	=> 0
		)))
			return ($this->setErrno(-18));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}

	/**
	 * POST
	 *
	 * Launch a POST Request on the API
	 *
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function post($apiRes, $apiParams = NULL)
	{
		// if (empty($apiRes))
		// 	die('Error: The resource cannot be empty.');

		if (!curl_setopt($this->m_curlResource, CURLOPT_POST, TRUE))
			return ($this->setErrno(-19));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}

	/**
	 * DELETE
	 *
	 * Launch a DELETE Request on the API
	 *
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function delete($apiRes, $apiParams = NULL)
	{
		// if (empty($apiRes))
		// 	die('Error: The resource cannot be empty.');

		if (!curl_setopt($this->m_curlResource, CURLOPT_POST, TRUE))
			return ($this->setErrno(-20));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}

	/**
	 * CUSTOM
	 *
	 * Launch a CUSTOM Request on the API
	 *
	 * @param[in]	$verb		Custom request verb
	 * @param[in]	$resource	Resource to be queried
	 * @param[in]	$parameters	array of parameters to be sent with the query
	 * @return		The response string
	 */
	public function custom($verb, $apiRes, $apiParams = NULL)
	{
		if (empty($verb)/* || empty($apiRes)*/)
			die('Error: The verb/resource cannot be empty.');

		if (!curl_setopt($this->m_curlResource, CURLOPT_CUSTOMREQUEST, $verb))
			return ($this->setErrno(-21));

		if (($req = $this->processRequest($apiRes, $apiParams)) === FALSE)
			return (FALSE);
		return ($req);
	}
}
?>