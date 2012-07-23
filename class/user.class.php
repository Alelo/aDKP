<?php
	/**
	 * a class for getting userdata
	 *
	 * @author Alelo (http://github.com/Alelo/)
	 * @copyright Alexander Loos(http://github.com/Alelo/) 2010
	 * @param string/int user_id oder user_name 
	 * @param array db host, user, pw, db
	  **/
	class user {
		protected $_user_id;
		protected $_user_login_name;
		protected $_user_roll;
		protected $_user_name;
		protected $_user_password;
		protected $_user_email;
		protected $_last_login;
		
		protected $_user_chars = array();
		
		protected $_user_shoutbox = array();
		protected $_user_news_comments = array();
		
		protected $_db;
		protected $_tmp;
		protected $_error = false;
		protected $_message;
		
		
		function __construct($user = false, $db = array()){
			if($user == false){
				$this->_message = "User Error, no User Information given";
				$this->_error = true;
				$this->__destruct();
			} else {
				$this->_db = new mysqli(DB_HOST, DB_USER,DB_PW, DB_DB);
				if (mysqli_connect_errno()){
					$this->_message="Connect failed: %s\n", mysqli_connect_error();
					$this->_error = true;
					$this->__destruct();
				}
				$this->_db->set_charset("utf8");
				if(is_int($user)){
					$this->_user_id = $user;
					$this->_tmp = $this->_db->query('SELECT user_name FROM user WHERE user_id = "'.$user.'";');
					$this->_user_name = $this->_tmp->fetch_row();
					$this->_tmp = null;
				} else {
					$this->_user_name = $user;
					$this->_tmp = $this->_db->query('SELECT user_id FROM user WHERE user_name = "'.$user.'";');
					$this->_user_id = $this->_tmp->fetch_row();
					$this->_tmp = null;
				}
			}
		}
		
		function __destruct(){
			if($this->_error){
				exit($this->_message);
			};
		}
		
		public function user_chars($full = false){
			if($full){
				$this->_tmp = $this->_db->query('SELECT char_id FROM user_chars WHERE user_id = "'.$this->_user_id.'";');
				while($char = $this->_tmp->fetch_assoc()){
					$chars = $this->_db->query('SELECT characters.char_id, characters.char_name, characters.char_guild, characters.char_class, characters.char_class, characters.char_race, characters.char_sex, characters.hide_char, redundant_dkp.dkp FROM characters, redundant_dkp WHERE redundant_dkp.char_id = characters.char_id AND characters.char_id = "'.(int)$char['char_id'].'";');
					while($data = $chars->fetch_assoc()){
						$this->user_chars[$chars['char_id']] = $data;
					}
				}
				$this->_tmp = null;
				
			} else {
				$this->_tmp = $this->_db->query('SELECT char_id FROM user_chars WHERE user_id = "'.$this->_user_id.'";');
				$this->_user_chars = $this->_tmp->fetch_assoc();
				$this->_tmp = null;
			}
			return $this->_user_chars;
		}
		
		public function roles(){
			if(empty($this->_user_roll)){
				$this->_tmp = $this->_db->query('SELECT admin_roll FROM admin WHERE user_id = "'.$this->_user_id.'";');
				$this->_user_roll = $this->_tmp->fetch_array();
				$this->_tmp = null;
			}
			return $this->_user_roll;
		}
		
		public function email(){
			if(empty($this->_user_email)){
				$this->_tmp = $this->_db->query('SELECT user_email FROM user WHERE user_id = "'.$this->_user_id.'";');
				$this->_user_email = $this->_tmp->fetch_row();
				$this->_tmp = null;
			}
			return $this->_user_email
		}
		
		public function password(){
			if(empty($this->_user_password)){
				$this->_tmp = $this->_db->query('SELECT user_password FROM user WHERE user_id = "'.$this->_user_id.'";');
				$this->_user_password = $this->_tmp->fetch_row();
				$this->_tmp = null;
			}
			return $this->_user_password;
		}
		
		public function user_name(){
			return $this->_user_name;
		}
		
		public function login_name(){
			if(empty($this->_user_login_name)){
				$this->_tmp = $this->_db->query('SELECT user_login FROM user WHERE user_id = "'.$this->_user_id.'";');
				$this->_user_login_name = $this->_tmp->fetch_row();
				$this->_tmp = null;
			}
			return $this->_user_login_name;
		}
		
		public function user_id(){
			return $this->_user_id;
		}
		
		public function shoutbox($count = 5){
			$this->_tmp = $this->_db->query('SELECT shout_id, shout_time, shout_text FROM shoutbox WHERE user_id = "'.$this->_user_id.'" '.(isset($count) && is_int($count))? "ORDER BY shout_time DESC LIMIT $count;" : 'ORDER BY shout_time DESC LIMIT 5;';
			$this->_user_shoutbox = $this->_tmp->fetch_array();
			$this->_tmp = null;
			return $this->_user_shoutbox;
		}
		
		
		public function news_comment($count = 5){
			$this->_tmp = $this->_db->query('SELECT news_com_id, news_com_time, news_com_text, news_id FROM news_comments WHERE user_id = "'.$this->_user_id.'" '.(isset($count) && is_int($count))? "ORDER BY news_com_time DESC LIMIT $count;" : 'ORDER BY new_com_time DESC LIMIT 5';
			$this->_user_news_comments = $this->_tmp->fetch_array();
			$this->_tmp = null;
			return $this->_user_news_comments;
		}
		
		
		public function last_login(){
			if(empty($this->_user_last_login)){
				$this->_tmp = $this->_db->query('SELECT date FROM user_last_login WHERE user_id = "'.$this->_user_id;
				$this->_user_last_login = $this->_tmp->fetch_row();
				$this->_tmp = null;
			}
			return $this->_user_last_login
		}
	}
?>