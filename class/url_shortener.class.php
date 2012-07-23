<?php
	/**
	 * a class that returns shortened urls
	 * based on an Deluxe Blog Tips Article(http://www.deluxeblogtips.com/2010/06/multiple-url-shortener-page.html)
	 *
	 * @author Alelo (http://github.com/Alelo/)
	 * @copyright Alexander Loos(http://github.com/Alelo/) 2010
	 * @param string $url Url that get shortened
	 **/
	class url_shortener {
		protected $_url;
		protected $_config = array();
		protected $_content;
		protected $_tmp = array();
		
		function __construct($url){
			$this->_url = $url;
		}
	
		protected function getContent($url) {
			if (function_exists('curl_init')) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$this->_content = curl_exec($ch);
				curl_close($ch);
			}
			elseif (ini_get('allow_url_fopen')) {
				$this->_content = file_get_contents($url);
			}
			return $this->_content;
		}
		
		public function changeurl($url){
			$this->_url = $url;	
		}
		public function get($name, $login='', $apikey=''){
			if($name == 'bitly' && (((empty($login) || empty($apikey))))){
				return 'Coult not connect to Bit.ly, Login & Apikey are required!';
			}
			return $this->$name($login, $apikey);
		}
		
		protected function bitly($login, $apikey) {
			return $this->getContent('http://api.bit.ly/v3/shorten?format=txt&longUrl='.$this->_url.'&login='.$login.'&apiKey='.$apikey);
		}
		
		protected function tinyurl() {
			return $this->getContent('http://tinyurl.com/api-create.php?url='.$this->_url);
		}
		
		protected function googl() {
			$data = $this->getContent('http://ggl-shortener.appspot.com/?url='.$this->_url);
			$json = json_decode($data);
			return $json->short_url;
		}
		
		protected function isgd() {
			return $this->getContent('http://is.gd/api.php?longurl='.$this->_url);
		}
		
		protected function all($login, $apikey){
			$this->_tmp['bitly'] = $this->bitly($login, $apikey);
			$this->_tmp['tinyurl'] = $this->tinyurl();
			$this->_tmp['googl'] = $this->googl();
			$this->_tmp['isgd'] = $this->isgd();
			return $this->_tmp;
		}

	}
?>