<?
class Application_Model_UploadImages
{
	private $_tempPath;
	private $_permanentPath;
	private static $_token;
	
	public static function getToken()
	{
		if (!isset(self::$_token))
		{
			$session = new Zend_Session_Namespace("uploads");
			if (!isset($session->token))
			{
				$session->token = rand(10000, 100000); 
			}
			self::$_token = $session->token;
		}
		return self::$_token;
	}
	
	public function Application_Model_UploadImages($tempPath, $permanentPath)
	{
		$this->_tempPath = $tempPath;
		$this->_permanentPath = $permanentPath;			
	}
	
	public function upload()
	{
		$uploadHandler = new Application_Model_Uploader(array(), 350 * 1024, self::getToken());
		$results = $uploadHandler->upload($this->_tempPath, true);    
		echo json_encode($results);
	}
	
	public function preview()
	{
		header('Content-type: image/jpg;');
		header("Cache-Control: no-cache, must-revalidate");
		readfile($this->_tempPath . self::getToken());
	}

	public function view($id)
	{
		header('Content-type: image/jpg;');
		readfile($this->_permanentPath . $id . ".jpg");
	}	

	public function save($id)
	{
		$source = $this->_tempPath . self::getToken();
		if (file_exists($source))
		{
			copy($source, $this->_permanentPath . $id . ".jpg");
			unlink($source);	
			return true;		
		}
		return false;
	}

	public function delete($id)
	{
		$file = $this->_permanentPath . $id . ".jpg";
		if (file_exists($file))
		{
			return unlink($file);	
			return true;	
		}
		return false;
	}
}