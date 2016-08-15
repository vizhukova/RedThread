<?

class PostikoApiChannel{
	
	private static $_SHOW_DEBUG = false;
	private static $_POSTIKO_API_ADDRESS = 'http://postiko.ru/cabinet/api/';
	private static $_POSTIKO_POST_PARAMS_PREPARATION_FUNCTION = array(
		'login'=>'trim',
		'pass'=>'trim',
		'method'=>'trim',
		'packages'=>'json_encode',
		'recipients'=>'json_encode',
		'text_message'=>'trim',
		'sender'=>'trim',
		'recipient'=>'trim',
		'key'=>'trim',
		'status_type'=>'trim',
	);
	
	private $_LOGIN = '';
	private $_PASS = '';
	
	/* ------ Common tracking options ------- */
	private $_TRACKING_OPTIONS = array(
		'sender'=>'',
		'seller_phone'=>'',
		'package_adding_sms'=>0,
		'package_adding_sms_template'=>0,
		'stage_client'=>0,
		'arrival_reminder'=>0,
		'count_days_reminder'=>3,
		'limit_number_of_repeated_notification'=>0,
		'stage_seller'=>0,
		'redelivery_stage_seller'=>0,
		'template_client_messages'=>0,
		'template_seller_messages'=>0,
		'template_client_reminder'=>0,
	);
	
	private $_ARR_MESSAGES_API_RESPONSE = array(
		'bad_auth'=>'Некорректный логин или пароль.',
		'must_auth'=>'Для выполнения этого метода требуется авторизация.',
		'array_packages_ids_more_then_allow'=>'Передаваемый массив посылок больше максимального разрешённого количества.',
		'bad_balance'=>'Недостаточный баланс.',
		'bad_packages_ids_array'=>'Переданы некорректные данные.',
		'incorrect_parametrs'=>'Переданы некорректные данные.',
		'wait'=>'Повторите запрос чуть позже.',
		'bad_signature'=>'Указано некорректное имя отправителя.',	
		'empty_array_recipients'=>'Массив получателей пустой',
	);
	
	private $_SUCCESS = array();
	private $_ERRORS = array();
	private $_MESSAGES = array();
	
	public function PostikoApiChannel($_LOGIN='',$_PASS=''){
		if(!empty($_LOGIN)) $this->_LOGIN = $_LOGIN;
		if(!empty($_PASS)) $this->_PASS = $_PASS;
	}
	
	
	public function debug($var,$_only_debug = false){
		if($_only_debug==false) $this->_MESSAGES[] = $var;
		if(self::$_SHOW_DEBUG==true || $_only_debug==true):
			echo "\n<pre class=\"debug\">\n ";
			$var = print_r($var, true);
			echo $var . "\n</pre>\n";	
		endif;
	}
	
	
	private function preparationParamsToCurlRequest($_REQUST_PARAMS_ARRAY=array()){
		if(is_array($_REQUST_PARAMS_ARRAY)):
			//$this->debug($_REQUST_PARAMS_ARRAY,true);
			$_RESULT_STRING = '';
			foreach($_REQUST_PARAMS_ARRAY as $_KEY_EL_ARR => $_VALUE_EL_ARR):
				//$this->debug($_KEY_EL_ARR.' - '.self::$_POSTIKO_POST_PARAMS_PREPARATION_FUNCTION[$_KEY_EL_ARR].'()',true);
				//$this->debug(self::$_POSTIKO_POST_PARAMS_PREPARATION_FUNCTION[$_KEY_EL_ARR],true);
				$_NAME_FUNCTION = ((isset(self::$_POSTIKO_POST_PARAMS_PREPARATION_FUNCTION[$_KEY_EL_ARR])) ? self::$_POSTIKO_POST_PARAMS_PREPARATION_FUNCTION[$_KEY_EL_ARR] : '');
				$_RESULT_STRING .= ((!empty($_NAME_FUNCTION)) ? ((!empty($_RESULT_STRING)) ? '&' : '').$_KEY_EL_ARR.'='.$_NAME_FUNCTION($_VALUE_EL_ARR) : '');
			endforeach;
			return trim($_RESULT_STRING);
		else:
			return '';
		endif;
	}
	
	private function processApiResponse($_API_RESPONSE_ARRAY){
		if(isset($_API_RESPONSE_ARRAY['messages']) && count($_API_RESPONSE_ARRAY['messages'])>0):
			foreach($_API_RESPONSE_ARRAY['messages'] as $_VALUE):
				$this->_MESSAGES[]=((isset($this->_ARR_MESSAGES_API_RESPONSE[$_VALUE])) ? $this->_ARR_MESSAGES_API_RESPONSE[$_VALUE] : $_VALUE);
			endforeach;;
		endif;
		return ((isset($_API_RESPONSE_ARRAY['result'])) ? $_API_RESPONSE_ARRAY['result'] : array());
	}
	
	private function curlRequest($_PARAMS_ARRAY=array(),$_METHOD='post'){
		if(is_array($_PARAMS_ARRAY) && count($_PARAMS_ARRAY)>0):
			
			$_PARAMS_STRING = $this->preparationParamsToCurlRequest($_PARAMS_ARRAY);
			//$this->debug($_PARAMS_STRING,true);
			$chp = curl_init(self::$_POSTIKO_API_ADDRESS);
			switch($_METHOD):
				case 'get':
					$chp = curl_init(self::$_POSTIKO_API_ADDRESS.'?'.$_PARAMS_STRING);
					curl_setopt($chp, CURLOPT_HEADER, 0);
					curl_setopt($chp, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($chp, CURLOPT_SSL_VERIFYPEER,0);
				break;
				
				default:
					$chp = curl_init(self::$_POSTIKO_API_ADDRESS);
					curl_setopt($chp, CURLOPT_HEADER, 0);
					curl_setopt($chp, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($chp, CURLOPT_SSL_VERIFYPEER,0);
					curl_setopt($chp, CURLOPT_POST, true);
					curl_setopt($chp, CURLOPT_POSTFIELDS, $_PARAMS_STRING);
				break;
				
			endswitch;	
			$result = curl_exec($chp);
			//$this->debug($result,true);
			$_ARR_RESULT = json_decode($result,true);
			//$this->debug($_ARR_RESULT,true);
			
			return $this->processApiResponse($_ARR_RESULT);
		else:
			return array();
		endif;	
	}
	
	private function cleanAndPreparationPackageID($_PACKAGE_ID=''){
		return trim(preg_replace('/[^\w\d]+/','',strtoupper($_PACKAGE_ID)));
	}
	
	private function cleanPostikoPackageID($_PACKAGE_ID=''){
		return trim(preg_replace('/[^\d]+/','',$_PACKAGE_ID));
	}
	
	private function cleanPhone($_PHONE=''){
		return preg_replace('/[^\d]+/','',$_PHONE);
	}
	
	private function checkPackageDataToAdd($_PACKAGE_ID='',$_PACKAGE_DATA=''){
		if(isset($_PACKAGE_DATA['client_phone'])) $this->cleanPhone($_PACKAGE_DATA['client_phone']);
		
		if(!empty($_PACKAGE_ID) && strlen($_PACKAGE_ID)>6 && is_array($_PACKAGE_DATA) && isset($_PACKAGE_DATA['client_phone']) && strlen($_PACKAGE_DATA['client_phone'])>6)	
			return true;
		else
			return false;	
	}
	
	public function returnResult($_RETURN='all'){
		switch($_RETURN):
			
			case 'success':
				return ((isset($this->_SUCCESS) && count($this->_SUCCESS)>0) ? $this->_SUCCESS : array());
			break;
			
			case 'errors':
				return ((count($this->_ERRORS)>0) ? $this->_ERRORS : array());
			break;
			
			case 'messages':
				return ((count($this->_MESSAGES)>0) ? $this->_MESSAGES : array());
			break;
			
			default:
				$_ARR_TO_RETURN = array();
				if(isset($this->_SUCCESS) && count($this->_SUCCESS)>0): $_ARR_TO_RETURN['success']=$this->_SUCCESS; endif;
				if(count($this->_ERRORS)>0): $_ARR_TO_RETURN['errors']=$this->_ERRORS; endif;
				if(count($this->_MESSAGES)>0): $_ARR_TO_RETURN['messages']=$this->_MESSAGES; endif;
				return $_ARR_TO_RETURN;
			break;
		endswitch;			
	}
	
	public function getMessages($_SHOW_MESSAGES = false){
		if($_SHOW_MESSAGES==true)
			$this->debug($this->_MESSAGES,true);
		else
			return $this->_MESSAGES;	
	}
	
	public function setTrackingOptions($_ARRAY_NEW_TRACKING_OPTIONS){
		$_REAL_OPTIONS = array_keys($this->_TRACKING_OPTIONS);
		foreach($_ARRAY_NEW_TRACKING_OPTIONS as $_KEY_OPTION_TO_SET => $_VALUE_OPTION_TO_SET):
			if(in_array($_KEY_OPTION_TO_SET,$_REAL_OPTIONS)): 
				if(in_array($_KEY_OPTION_TO_SET,array('sender','seller_phone')))
					$this->_TRACKING_OPTIONS[$_KEY_OPTION_TO_SET] = $_VALUE_OPTION_TO_SET;
				else
					$this->_TRACKING_OPTIONS[$_KEY_OPTION_TO_SET] = (int)$_VALUE_OPTION_TO_SET;
			endif;	
		endforeach;		
		return $this;
	}	
	
	public function addPackages($_ARRAY_PACKAGES=array(),$_RETURN='all'){
		if(is_array($_ARRAY_PACKAGES) && count($_ARRAY_PACKAGES)>0):
			$_ARRAY_TO_REQUEST = array();
			
			foreach($_ARRAY_PACKAGES as $_KEY_PACKAGE => $_VALUE_ARR_PACKAGE):
				$_ORIGINAL_KEY_PACKAGE = $_KEY_PACKAGE;
				$_KEY_PACKAGE = $this->cleanAndPreparationPackageID($_KEY_PACKAGE);
				if($this->checkPackageDataToAdd($_KEY_PACKAGE,$_VALUE_ARR_PACKAGE)):
					if(!isset($_VALUE_ARR_PACKAGE['desc']) || empty($_VALUE_ARR_PACKAGE['desc'])) $_VALUE_ARR_PACKAGE['desc'] = 'Посылка';
					$_ARRAY_TO_REQUEST[$_KEY_PACKAGE]=array_merge($this->_TRACKING_OPTIONS,$_VALUE_ARR_PACKAGE);
				else:
					$this->_ERRORS[$_ORIGINAL_KEY_PACKAGE]=$_VALUE_ARR_PACKAGE;
					$this->_MESSAGES[] = $this->_ARR_MESSAGES_API_RESPONSE['bad_packages_ids_array'];
				endif;	
			endforeach;
			
			//$this->debug($_ARRAY_TO_REQUEST,true);
			
			if(count($_ARRAY_TO_REQUEST)>0):
				$this->_SUCCESS = $this->curlRequest(array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'add_packages','packages'=>$_ARRAY_TO_REQUEST));
				
				//$this->debug($_RESULT_ARRAY,true);
				
				$_ARR_BAD_PACKAGES_BU_RSULTS_API = array_diff(array_keys($_ARRAY_PACKAGES),array_keys($this->_SUCCESS));
				foreach($_ARR_BAD_PACKAGES_BU_RSULTS_API as $_BAD_PACKAGE_ID):
					if(!isset($this->_ERRORS[$_BAD_PACKAGE_ID]))
						$this->_ERRORS[$_BAD_PACKAGE_ID] = $_ARRAY_PACKAGES[$_BAD_PACKAGE_ID];		
				endforeach;
				//$this->debug($_RESULT_ARRAY,true);
			endif;
			
		else:
			$this->debug('Массив посылок не соответствует нужному формату.');
		endif;
		
		return $this->returnResult($_RETURN);		
	}
	
	
	public function getPackagesInfo($_ARRAY_POSTIKO_IDS=array()){
		if(is_array($_ARRAY_POSTIKO_IDS) && count($_ARRAY_POSTIKO_IDS)>0):
			$_ARRAY_TO_REQUEST = array();
			
			foreach($_ARRAY_POSTIKO_IDS as $_VALUE_POSTIKO_ID_PACKAGE):
				$_ORIGINAL_KEY_PACKAGE = $_VALUE_POSTIKO_ID_PACKAGE;
				$_VALUE_POSTIKO_ID_PACKAGE = $this->cleanPostikoPackageID($_VALUE_POSTIKO_ID_PACKAGE);
				//$this->debug($_VALUE_POSTIKO_ID_PACKAGE,true);
				if(!empty($_VALUE_POSTIKO_ID_PACKAGE)):
					$_ARRAY_TO_REQUEST[]=$_VALUE_POSTIKO_ID_PACKAGE;
				else:
					$this->_ERRORS[]=$_ORIGINAL_KEY_PACKAGE;
					$this->_MESSAGES[] = $this->_ARR_MESSAGES_API_RESPONSE['bad_packages_ids_array'];
				endif;	
			endforeach;
			
			//$this->debug($_ARRAY_TO_REQUEST,true);
			
			if(count($_ARRAY_TO_REQUEST)>0):
				$this->_SUCCESS = $this->curlRequest(array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'get_packages_info','packages'=>$_ARRAY_TO_REQUEST));
				
				//$this->debug($_RESULT_ARRAY,true);
				
				$_ARR_BAD_PACKAGES_BU_RSULTS_API = array_diff($_ARRAY_POSTIKO_IDS,array_keys($this->_SUCCESS));
				foreach($_ARR_BAD_PACKAGES_BU_RSULTS_API as $_BAD_PACKAGE_ID):
					if(!in_array($_BAD_PACKAGE_ID,$this->_ERRORS))
						$this->_ERRORS[] = $_BAD_PACKAGE_ID;		
				endforeach;
				//$this->debug($_RESULT_ARRAY,true);
			endif;
			
		else:
			$this->debug('Массив посылок не соответствует нужному формату.');
		endif;
		
		return $this->returnResult($_RETURN);		
	}
	
	public function getErrorAddingPackages(){
		return $this->_ERROR_ADDING_PACKAGES_ARRAY;
	}
	
	
	public function changePackagesStatus($_ARRAY_PACKAGES=array()){
		if(is_array($_ARRAY_PACKAGES) && count($_ARRAY_PACKAGES)>0):
			$_ARRAY_TO_REQUEST = array();
			
			foreach($_ARRAY_PACKAGES as $_KEY_PACKAGE => $_VALUE_PACKAGE_STATUS):
				$_ORIGINAL_KEY_PACKAGE = $_KEY_PACKAGE;
				$_KEY_PACKAGE = $this->cleanPostikoPackageID($_KEY_PACKAGE);
				if(!empty($_KEY_PACKAGE) && in_array($_VALUE_PACKAGE_STATUS,array(1,2))):
					
					$_ARRAY_TO_REQUEST[$_KEY_PACKAGE]=$_VALUE_PACKAGE_STATUS;
				else:
					$this->_ERRORS[$_ORIGINAL_KEY_PACKAGE]=$_VALUE_PACKAGE_STATUS;
					$this->_MESSAGES[] = $this->_ARR_MESSAGES_API_RESPONSE['bad_packages_ids_array'];
				endif;	
			endforeach;
			
			if(count($_ARRAY_TO_REQUEST)>0):
				$this->_SUCCESS = $this->curlRequest(array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'change_packages_status','packages'=>$_ARRAY_TO_REQUEST));
				
				//$this->debug($_RESULT_ARRAY,true);
				
				$_ARR_BAD_PACKAGES_BU_RSULTS_API = array_diff(array_keys($_ARRAY_PACKAGES),array_keys($this->_SUCCESS));
				foreach($_ARR_BAD_PACKAGES_BU_RSULTS_API as $_BAD_PACKAGE_ID):
					if(!isset($this->_ERRORS[$_BAD_PACKAGE_ID]))
						$this->_ERRORS[$_BAD_PACKAGE_ID] = $_ARRAY_PACKAGES[$_BAD_PACKAGE_ID];		
				endforeach;
				//$this->debug($_RESULT_ARRAY,true);
			endif;
		else:
			$this->debug('Массив посылок не соответствует нужному формату.');
		endif;
		return $this->returnResult($_RETURN);	
	}
	
	public function getBalance($_RETURN){
		$this->_ERRORS = array();
		$this->_MESSAGES = array();
		$this->_SUCCESS = $this->curlRequest(array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'get_balance'));
		return $this->returnResult($_RETURN);	
	}
	
	public function sendSMS($_RECIPIENTS = array(), $_TEXT = '', $_SENDER = ''){
		if(is_array($_RECIPIENTS) && count($_RECIPIENTS)>0 && !empty($_TEXT)):
			
			$_GOOD_RECIPIENTS = array();
			foreach($_RECIPIENTS as $_VALUE_RECIPIENT):
				if(preg_match("/[A-Za-zАаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя]{1,}/", $_VALUE_RECIPIENT))
					$this->_ERRORS[] = $_VALUE_RECIPIENT;	
				else
					$_GOOD_RECIPIENTS[]=$_VALUE_RECIPIENT;		
			endforeach;
			
			$_ARR_TO_REQUEST = array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'send_sms', 'text_message'=>$_TEXT, 'recipients'=>$_GOOD_RECIPIENTS);
			if(!empty($_SENDER)) $_ARR_TO_REQUEST['sender']=$_SENDER;
			//$this->debug($_ARR_TO_REQUEST,true);
			$this->_SUCCESS = $this->curlRequest($_ARR_TO_REQUEST);
		else:
			//$this->debug(2,true);
			$this->debug('Переданы некорректные данные.');
		endif;
		return $this->returnResult($_RETURN);	
	}
	
	
	public function getPackagesInfoByPhone($_PHONE='',$_KEY='track_numb',$_status_type='array'){
		$_PHONE=$this->cleanPhone($_PHONE);
		//$this->debug($_PHONE);
		if(!empty($_PHONE)):
			//$this->debug($_PHONE);
			$this->_SUCCESS = $this->curlRequest(array('login'=>$this->_LOGIN,'pass'=>$this->_PASS,'method'=>'get_packages_data_by_recipient_numb','recipient'=>$_PHONE, 'key'=>$_KEY, 'status_type'=>$_status_type));
		else:
			$this->_MESSAGES[] = $this->_ARR_MESSAGES_API_RESPONSE['incorrect_parametrs'];
		endif;
		return $this->returnResult($_RETURN);
		
	}
}

?>