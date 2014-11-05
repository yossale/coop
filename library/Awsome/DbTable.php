<?
abstract class Awsome_DbTable
{
	protected $adapter;
	
	protected $tableName;
	protected $editableColumns;
	protected $nameColumn;
	protected $primaryColumn;
	protected $deleteColumn;
	protected $orderBy;
	protected $neededFiles = array();
	protected $errors = array();
	
	public function __construct()
	{
		$this->adapter = Zend_Db_Table::getDefaultAdapter();
	}
	
	protected function filterParams($params)
	{
		$return = array();
		foreach ($params as $key => $value)
		{
			if (in_array($key, $this->editableColumns))
			{
				$return[$key] = $value;
			}
		}
		return $return;
	}
	
	protected function getAll($select_params = "*", $additional_where = "")
	{
		$sql = "SELECT $select_params FROM {$this->tableName}";
		if (!empty($this->deleteColumn))
		{
			$sql .= " WHERE {$this->deleteColumn} = 0";
		}		
		if (!empty($additional_where))
		{
			$sql .= " AND $additional_where";
		}
		if (!empty($this->orderBy))
		{
			$sql .= " ORDER BY {$this->orderBy}";
		}
		return $this->adapter->fetchAssoc($sql);
	}
	
	protected function getOne($id, $select_params = "*")
	{
		$sql = "SELECT $select_params FROM {$this->tableName}  WHERE";
		if (!empty($this->deleteColumn))
		{
			$sql .= " {$this->deleteColumn} = 0 AND";
		}		
		$sql .= $this->adapter->quoteInto(" {$this->primaryColumn} = ?", $id);
		$row = $this->adapter->fetchRow($sql);
		if (!$row)
		{
			throw new Exception("no row in {$this->tableName} where {$this->primaryColumn} = $id ");
		}
		return $row;
	}
	
	protected function checkExists($id)
	{
		if (empty($id))
		{ 
			throw new Exception("empty id in {$this->tableName}");
		}
		$this->getOne($id, $this->primaryColumn);
	}
	
	protected function add($data)
	{
		if (!$this->isValid(false))
		{
			throw new Exception('Data is not valid');
		}
		
		$this->adapter->insert($this->tableName, $this->filterParams($data));
		$id = $this->adapter->lastInsertId();
		
		$this->uploadFiles($id);
		return $id;
	}
	
	protected function edit($id, $data)
	{	
		if (!$this->isValid(true))
		{
			throw new Exception('Data is not valid');
		}
		
		$this->uploadFiles($id);
		
		$this->checkExists($id);
		$where = $this->adapter->quoteInto("$this->primaryColumn = ?", $id);
		$this->adapter->update($this->tableName, $this->filterParams($data), $where);
	}
	
	protected function delete($id)
	{
		$this->checkExists($id);
		$where = $this->adapter->quoteInto("$this->primaryColumn = ?", $id);
		$this->adapter->update($this->tableName, array("{$this->deleteColumn}" => 1), $where);
	}

	
	protected function getAllForHTMLSelectBox()
	{
		$rows = $this->getAll($this->primaryColumn . ", " . $this->nameColumn);
		
		$options = array();
		foreach ($rows as $row)
		{
			$options[$row[$this->primaryColumn]] = stripslashes($row[$this->nameColumn]);
		}
		return $options;
		
	}
	
	protected function setAsChildOfArray($child_array, $parent_array, $combining_key)
	{
		$returned_array = array();
		
		foreach ($parent_array as $parent_item)
		{
			$found = array();
			foreach ($child_array as $child_item)
			{
				if ($child_item[$combining_key] == $parent_item[$combining_key])
				{
					$found[] = $child_item;
				}
			}
			if (count($found) != 0)
			{
				$parent_item[$this->tableName] = $found;
				$returned_array[] = $parent_item;
			}
		}
		
		return $returned_array;
	}
	
	protected function getCurrentDateTime()
	{
		return date("Y-m-d H:i:s");
	}
	
	protected function getCurrentDate()
	{
		return date("Y-m-d");
	}
		
	protected function isValid($editMode)
	{
		return ($this->isValidFiles($editMode));
	}
	
	protected function getErrors()
	{
		return $this->errors;
	}
	
	protected function isValidFiles($editMode)
	{
		if (count($this->neededFiles) > 0)
		{
			foreach ($this->neededFiles as $file)
			{
				if ($editMode)
				{
					$file->setMustHave(false);
				}
				
				if (!$file->isValid())
				{
					$errors = $file->getErrors();
					$this->errors[$file->getName()] = array();
					foreach ($errors as $name => $value)
					{
						$this->errors[$file->getName()][$name] = $value;
					}
					return false;
				}
			}
		}
		return true;
	}
	
	protected function uploadFiles($id)
	{
		if (count($this->neededFiles) > 0)
		{
			foreach ($this->neededFiles as $file)
			{
				$file->replaceVarible("{%id%}", $id);
				$file->upload();
			}
		}
		return true;		
	}
}