<?
class Coop_Categories extends Awsome_DbTable
{	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "product_categories";
		$this->editableColumns = array("coop_id", "category_name", "category_list_position");
		$this->nameColumn = "category_name";
		$this->primaryColumn = "category_id";
		$this->deleteColumn = "category_deleted";
		$this->orderBy = "category_list_position";
	}
	
	public function getAllCategories($coop_id)
	{		
		$sql = "SELECT * FROM product_categories WHERE coop_id = " . (int)$coop_id . " AND category_deleted = 0 ORDER by category_list_position";
		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;
	}
		
	public function getAllCategoriesForHTMLSelectBox($coop_id)
	{
		$rows = $this->getAllCategories($coop_id);
		
		$options = array();
		if (!empty($rows))
		{
			foreach ($rows as $row)
			{
				$options[$row[$this->primaryColumn]] = stripslashes($row[$this->nameColumn]);
			}			
		}
		return $options;
	}
		
	public function getCategory($id)
	{
		return $this->getOne($id);
	}
	
	public function addCategory($coop_id, $data)
	{
		$data['coop_id'] = $coop_id;
		return $this->add($data);
	}
	
	public function editCategory($id, $data)
	{		
		return $this->edit($id, $data);
	}
	
	public function deleteCategory($id)
	{
		return $this->delete($id);
	}

}