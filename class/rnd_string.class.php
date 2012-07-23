<?php
	/**
	 * a class that returns a rnd string
	 *
	 * @author Alelo (http://github.com/Alelo/)
	 * @copyright Alexander Loos(http://github.com/Alelo/) 2010
	 * @param int $len lengt of the string
	 * @param string $base base characters/numbers of the string
	 **/
	class rnd_string {
		protected $_string, $_base, $_len, $_max;
		
		function __construct($len = 10, $base = '0123456789abcdefghjkmnpqrstwxyzABCDEFGHKLMNOPQRSTWXYZ'){
			$this->_len = (int)$len;
			$this->_base = $base;
			$this->_max = strlen($this->_base)-1;
			mt_srand((double)microtime()*1000000);
		}
	
		public function get() {
			$this->_string = '';
			while (strlen($this->_string)<$this->_len){
				$this->_string .= $this->_base{mt_rand(0,$this->_max)};
			}
			return $this->_string;
		}

	}
?>