<?php
	/**
	 * a class that catches your rewritten urls and makes an array of it
	 *
	 * @author Alelo (http://github.com/Alelo/)
	 * @copyright Alexander Loos(http://github.com/Alelo/) 2010
	 * @param string $divider = divider for url 
	 * @param boolean $extension = show file extensions
	**/
	class url_rewrite {
		protected $_path;
		protected $_pathinfo = array();
		protected $_pathinfonoext = array();
		protected $_divider;
		protected $_tmp_get;
		
		function __construct($divider = '/'){
			$this->_divider = $divider;
			if($_SERVER['REQUEST_URI'] == '/'){
				return array(null, null);
			}
			list($this->_path) = explode('?', $_SERVER['REQUEST_URI']);
			$this->_path = substr($this->_path, strlen(dirname($_SERVER['SCRIPT_NAME'])));
			$this->makearray();
			$this->removeext();
		}
		
		protected function makearray(){
			foreach (explode($this->_divider, $this->_path) as $index=>$dir) {
				if (!empty($dir))$this->_pathinfo[$index] = $dir;
			}
		}
		
		protected function removeext() {
			if (count($this->_path) > 0) {
				$last = $this->_pathinfo[count($this->_path)];
				list($lastvar) = explode('.', $last);
				$this->_pathinfonoext = $this->_pathinfo;
				$this->_pathinfonoext[count($this->_path)] = $lastvar;
			}
		}
		
		public function returnArray($extension = true){
			return ($extension)? $this->_pathinfo : $this->_pathinfonoext;
		}
		
		public function make_get($extension = true){
			foreach(($extension)? $this->_pathinfo : $this->_pathinfonoext as $key=>$value ){
				if($key % 2){
					$_GET[$this->_tmp_get] = $value;
				} else {
					$_GET[$value] = null;
					$this->_tmp_get = $value;
				}
			}
		}
	}
?>