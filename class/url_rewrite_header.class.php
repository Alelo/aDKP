<?php
	require_once('url_rewrite.class.php');
	/**
	 * a class that extends the url_rewrite class and adds an header return
	 *
	 * @author Alelo (http://github.com/Alelo/)
	 * @copyright Alexander Loos(http://github.com/Alelo/) 2010
	 * @param string $divider  divider for url splitting
	 * @param string $title a requiered string (name of the site)
	 * @param boolean $extension  show file extensions
	 * @param string $dividerheader divider for  header
	 * @param boolean $reverse false for string return ltr , true for string return rtl
	 **/
	class url_rewrite_header extends url_rewrite {
		private $_header = '';
		
		public function returnHeader($title, $divider = '-', $extension = false, $reverse = false) {
			$divider = ' '.$divider.' ';
			$headerarray = ($extension)? $this->_pathinfo : $this->_pathinfonoext;
			$headerarray = ($reverse)? array_reverse($headerarray): $headerarray;
			if($headerarray[0] !== null){
				foreach($headerarray as $key => $path){
					if($reverse){
						$this->_header .= ($key == 0) ? $path : $divider.$path;
					} else {
						$this->_header .= ($key == count($headerarray)-1) ? $path : $path.$divider;
					}
				}
				$this->_header = ($reverse)? urldecode($this->_header.$divider.$title) : urldecode($title.$divider.$this->_header);
			}
			return $this->_header;
		}
	}
?>