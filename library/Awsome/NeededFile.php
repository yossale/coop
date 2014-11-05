<?
class Awsome_NeededFile
{
	protected $_name;
	protected $_mustHave;
	protected $_maxSize;
	protected $_type;
	protected $_saveDir;	
	protected $_fileName;
	
	protected $_errors;
	protected $_uploadedFile;
	
	public function __construct($name, $mustHave, $maxSizeInKB, $type, $saveDir, $fileName)
	{
		if ($type != "Image")
		{
			throw new Exception('No such type as ' . $type);
		}
		if (!is_dir($saveDir))
		{
			throw new Exception('No such dir as ' . $saveDir);
		}
		if (!is_writeable($saveDir))
		{
			throw new Exception($saveDir . ' is not writable');
		}
		
		$this->_name = $name;
		$this->_mustHave = $mustHave;
		$this->_maxSize = $maxSizeInKB * 1024;
		$this->_type = $type;
		$this->_saveDir = $saveDir;
		$this->_fileName = $fileName;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function setMustHave($value)
	{
		$this->_mustHave = $value;
	}	
	
	public function isValid()
	{
		$file = $_FILES[$this->_name];	
		
		// Exists?
		if (empty($file['name']))
		{
			$this->_uploadedFile = false;
			if ($this->_mustHave)
			{
				$this->_errors['NO_FILE']= "No file";	
				return false;		
			}
			else 
			{
				return true;
			}			
		}
		
		$this->_uploadedFile = true;
		
		// PHP error?
		if ($file['error'] != 0)
		{
			$this->_errors['PHP_ERROR'] = "PHP error while uploading file. Error: " . $file['error'];
		}
		
		// Size?
		if ($file['size'] > $this->_maxSize)
		{
			$this->_errors['TOO_BIG'] = "File is too big. file is " . $file['size'] . ' bytes and max is ' . $this->_maxSize . ' bytes';
		}
		
		// Type?
		switch ($this->_type) {
			case "Image":
				$explode = explode("/", $file['type']);
				$match_type = ($explode[0] == "image");
				$type_msg = "Not an image type of flie";
				break;
		}
		if (!$match_type)
		{
			$this->_errors['INVALID_TYPE'] = $type_msg;
		}
		
		return (count($this->_errors) == 0);
	}
	
	public function getErrors()
	{
		return $this->_errors;
	}
	
	public function replaceVarible($name, $value, $filter = "")
	{
		if (!empty($filter))
		{		
			switch ($filter) {
				case "Integer":
					$valid = (is_integer($value));
					break;
					
				default:
					throw new Exception('no such filter');
					break;
			}
			if (!$valid)
			{
				throw new Exception('not valid to replace varible');
			}
		}
		
		$this->_fileName = str_replace($name, $value, $this->_fileName);
	}
	
	public function upload()
	{			
		if (!$this->isValid())
		{
			throw new Exception('Cannot upload a file that is not valid');
		}
		if (!$this->_uploadedFile)
		{
			return false;
		}
		
		$filename = $this->_saveDir . '/' . $this->_fileName;	
		$attempt = move_uploaded_file($_FILES[$this->getName()]['tmp_name'], $filename);
		if (!$attempt)
		{
			throw new Exception("Could not upload the file: " . $filename);
		}
	}
	
}