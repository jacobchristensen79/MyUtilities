<?php

/**
 * 
 * @author J.Christensen
 * @version 0.1b
 * @copyright
 *
 */
if (!defined('ENVIRONMENT')) die('Direct access not permitted');

use Classes\AppException as AppException;
use Classes\FrmHelper as helper;
use Models\User;
use Classes\FrmPseudoCode;
use Models\Domain;
use Models\DomainType;
use Models\Url;

class App {
	private $_errors = array();
	private $_templateFolder = 'pages';
	private $_languageFolder = 'Config/language';
	private $_lang;
	private $_trans = array();
	private $_languages = array();
	
	// Intern Use
	private $_website;
	private $_routing;
	private $_requestedPage = false;
	private $_getRequest = array();
	private $_username;
	private static $_instance;
	
	// Render Data, form, image...
	private $_renderData = array();

	public function __construct() 
	{
		$this->_languageFolder = DIRECTORY_SEPARATOR . $this->_languageFolder;
		$this->_templateFolder = DIRECTORY_SEPARATOR . $this->_templateFolder;
		$this->_lang = default_lang;
		$this->setEnvironment();
	}
	
	public static function getInstance()
	{		
		if (  !self::$_instance instanceof self)
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	

	
	public function getErrors() {
		return $this->_errors;
	}

	public function getTranslations()
	{
		return $this->_trans;
	}
	
	public function getLang()
	{
		return $this->_lang;
	}
	
	public function getRouting()
	{
		return $this->_routing;
	}
	
	public function getRequestedPage()
	{
		return $this->_requestedPage;
	}
	
	public function getWebsite()
	{
		return $this->_website;
	}
	

	/**
	 * For Server, Uri and query, language
	 * _request
	 */
	private function setEnvironment()
	{
		$sesStarted = (session_status() === PHP_SESSION_ACTIVE) ? true : false;
		if ( !$sesStarted ) session_start();
		
		$http = isset($_SERVER['HTTPS']) ? "https" : 'http';
		$url = $http.'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parsed = parse_url($url);
		$this->_website = $http.'://'.$parsed['host'];
	
		// Routing File
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'routing.php';
		if (!is_file($file)) return false;
		include $file;
		$this->_routing = $_routing;
		$this->_languages = $_languages;
	
		// Clean query
		$query = array_filter(explode('/',$parsed['path']));
		if(!empty($query)) $query = array_slice($query, 0);
		
		// Fast if GoTo
		if ( isset($query[0]) && isset($query[1]) ) {
			foreach ($this->_routing as $lang=>$r){
				if ('goto' == array_search($query[0], $r)) {
					$urlId = FrmPseudoCode::unhash($query[1]);
					$urlmodel = New Url();
					$url = $urlmodel->getById($urlId);
					if ($url){
						$url->updateClicks();
						header('location: '.$url->service_link);
						exit();
					}
				}
			}
		}
		
		// Language
		if ( isset($query[0]) && array_key_exists($query[0], $this->_languages) ){
			$this->_lang = $query[0];
			$_SESSION['userlanguage'] = $this->_lang;
			unset($query[0]);
			if(!empty($query)) $query = array_slice($query, 0);
		} elseif(isset($_SESSION['userlanguage']) && !empty($_SESSION['userlanguage'])) {
			$this->_lang = $_SESSION['userlanguage'];
		} else {
			$browserlang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : false;
			if ($browserlang && array_key_exists($browserlang, $this->_languages)) {
				$this->_lang = $browserlang;
				$_SESSION['userlanguage'] = $browserlang;
			}
		}
		
		// Page QUery
		if ( !empty( $query ) ) {
			$this->_getRequest = $query;
			$p = implode('/', $query);
			if ( isset($this->_routing[$this->_lang]) && in_array($p, $this->_routing[$this->_lang]) ) {
				$this->_requestedPage = array_search($p, $this->_routing[$this->_lang]);
			}
			// Default page
		} elseif ( isset($this->_routing[$this->_lang]) ) {
			$this->_requestedPage = array_search('/', $this->_routing[$this->_lang]);
		}
	}
	
	public function getOutputMethod()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			return 'ajax';
		} else {
			return 'html';
		}
	}


	public function loadLanguages($page) {
		$dir = __DIR__ . DIRECTORY_SEPARATOR  . $this->_languageFolder . DIRECTORY_SEPARATOR . $this->_lang;
		if (!is_dir($dir))
			return false;

		include $dir . DIRECTORY_SEPARATOR . 'all.php';
		$this->_trans['all'] = $trans;

		if (is_file($dir . DIRECTORY_SEPARATOR . $page . '.php')) {
			include $dir . DIRECTORY_SEPARATOR . $page . '.php';
			$this->_trans['page'] = $trans;
		} else {
			include $dir . DIRECTORY_SEPARATOR . 'error404.php';
			$this->_trans['page'] = $trans;
		}

	}


	/**
	 * Render a template 
	 * @return html string
	 * @param string $template
	 * @param arrag key value for template output
	 */
	public function render($template, $data = array()) {
		$_languages = $this->_languages;
		$_lang = $this->_lang;
		$_routing = $this->_routing;
		$this->_renderData = array_merge($this->_renderData, $data);
		$this->_renderData['_username'] = $this->_username;
		
		$templateFile = __DIR__ . DIRECTORY_SEPARATOR . $this->_templateFolder . DIRECTORY_SEPARATOR . $template . '.php';
		if (!file_exists($templateFile)) return ' <h3> template incorrecto </h3> ' . $templateFile;
		
		$string = '';
		ob_start();
		extract($this->_renderData);
		include $templateFile;
		$string = ob_get_contents();
		ob_end_clean();
		return $string;
	}

	public function getToken() {
		$ip = helper::getClientIpAddr();
		return sha1('SecurityToken_' . $ip);
	}
	
	/**
	 * App Session Manager
	 * @param obj $user
	 */
	public function sessionStart(\Models\User $user=NULL)
	{
		if ( $user ){
			$_SESSION[session_name] = $user->username;
			$_SESSION[session_name.'_id'] = $user->id;
			$url = self::url('home');
			header('location: /'.$url);
			exit();
		}		
		
		// if no user go to login
		if ( !isset($_SESSION[session_name]) || empty($_SESSION[session_name]) ){
			
			if ( $this->_requestedPage != 'login') {
				$url = self::url('login');
				header('location: /'.$url);
				exit();
			}	
		} else {
			$this->_username = $_SESSION[session_name];
		}
	}
	
	/**
	 * Close Session
	 */
	public function logout()
	{
		if ( isset($_SESSION[session_name])) unset($_SESSION[session_name]);
		$url = self::url('login', $this->_lang);
		header('location: '.$url);
		exit();		
	}

	/**
	 * Login Session
	 * @return bool
	 */
	public function login() 
	{
		// No es POST
		if (!filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS)) {
			return 'login';
		}

		$token = $this->getToken();

		$username = filter_input(INPUT_POST, "access_username", FILTER_SANITIZE_SPECIAL_CHARS);
		$password = filter_input(INPUT_POST, "access_password", FILTER_SANITIZE_SPECIAL_CHARS);

		// Token Verifier
		if ($token != filter_input(INPUT_POST, "_token", FILTER_SANITIZE_SPECIAL_CHARS)) {
			$this->_errors['_token'] = 'Token error';
		}
		
		// Login
		if ( $username && $password ){
			$usermodel = new User();
			$user = $usermodel->getByUsername($username);
			if ( $user && $user->password == sha1($password) ) {
				if ( 'disabled' == $user->getStatus()) {
					$this->_errors['incorrect'] = 'User has been disabled';
				} else {
					$this->sessionStart($user);
				}
			} else {
				$this->_errors['incorrect'] = 'Password is incorrect';
			}			
		} else {
			$this->_errors['incorrect'] = 'Username or password is incorrect';
		}

		
		$this->_renderData['errors'] = $this->_errors;
		return false;

	}
	
	public function insertNewUrl()
	{
		$posturl = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
		if (!$posturl) return array('result'=>false, 'message'	=> 'Incorrect url');
		
		$userId = $_SESSION[session_name.'_id'];
		
		$domain = str_ireplace('www.', '', parse_url($posturl, PHP_URL_HOST));		
		$domainType = ($t = explode('.', $domain)) ? end($t) : false;
		
		// Domaind Type
		$modelType = new DomainType();
		$objType = $modelType->getByName($domainType);
		if ( $objType->getId() ){
			$typeId = $objType->getId();
			$objType->updateQuantity();
		} else {
			$objType->name = $domainType;
			$typeId = $objType->save();
		}
		
		// Domaind Control
		$modelDomaind = new Domain();
		$objDomain = $modelDomaind->getByName($domain);
		if ( $objDomain->getId() ){
			$domaindId = $objDomain->getId();
			$objDomain->updateQuantity();
		} else {
			$objDomain->name = $domain;
			$objDomain->service_domain_type_id = $typeId;
			$domaindId = $objDomain->save();
		}
			
		// URL Control
		$url = new Url();
		$url->service_link = $posturl;
		$url->service_user_id = $userId;
		$url->service_domaind_id = $domaindId;
		$urlId = $url->save();
		
		// Code Lenght
		$keyLen = 4;
		if ( $urlId > 56800235584 ) $keyLen = 7;
		elseif ( $urlId > 916132832 ) $keyLen = 6;
		elseif ( $urlId > 14776336 ) $keyLen = 5;
		
		$code = FrmPseudoCode::hash($urlId, $keyLen);
		$url->shortcode = $code;
		$url->updateCode();		
		
		$urlLink = App::url('goto', $this->_lang, $code, true);
		
		return array(
			'result'	=> true,
			'message' 	=> 'New url has been generated',
			'link'		=> $urlLink,
			'total'		=> 100
		);
	}
	
	public function getProfileData()
	{
		$model = new Domain();
		$domains = $model->getTop();
		
		$model = new DomainType();
		$domaintypes = $model->getTop();
		
		$model = new Url();
		$links = $model->getTopClicks();
		$usertoplinks = $model->getTopByUserId($_SESSION[session_name.'_id']);
		$userlinks = $model->getByUserId($_SESSION[session_name.'_id']);
		
		return array(
				'domains'		=> $domains, 
				'domaintypes'	=> $domaintypes,
				'links'			=> $links,
				'usertoplinks'	=> $usertoplinks,
				'userlinks'		=> $userlinks
		);
	}

	/**
	 * Translate
	 * @param string $type ( msg or error ) 
	 */	
	public static function translate($string, $type='msg')
	{
		$translations = App::getInstance()->getTranslations();
		if ( isset($translations[$type]) && isset($translations[$type][$string]))
			return $translations[$type][$string];
		else
			return $string;		
	}
	
	
	/**
	 * URL generate
	 */
	public static function url( $action, $lang=false, $params=false, $full = false)
	{
		$app = App::getInstance();
		$routing = $app->getRouting();
		$translations = $app->getTranslations();
		$website = $app->getWebsite();
		$lang = ($lang) ? $lang : $app->getLang();
		
		// Route
		$route = ( isset($routing[$lang]) && isset($routing[$lang][$action]) ) ? $routing[$lang][$action] : 'not found';
		
		if ( empty($params) ) {
			return ($full) ? $website.'/'.$route : $lang.'/'.$route;
		} else {
			if ( is_array($params)) {
				$str = '';
				foreach ($params as $k=>$v) $str .= $k.'/'.$v;
			} else {
				$str = $params;
			}
			return  ($full) ? $website.'/'.$route.'/'.$str : $route.'/'.$lang.'/'.$str;
		}
	
	}

}
