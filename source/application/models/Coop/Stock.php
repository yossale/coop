<?
class Coop_Stock extends Awsome_DbTable 
{
	public function getStock($reset_day)
	{
		$sql = "SELECT * FROM stock WHERE coop_reset_day = '$reset_day'"; 
		
		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;
	}
	
	public function setStock($reset_day, $amounts, $comments, $coop_id)
	{		
		// clear before edit
		$sql = "DELETE FROM stock WHERE coop_reset_day = '$reset_day'";
		$this->adapter->query($sql);
		
		// grab amounts
		$updates = array();		
		foreach ($amounts as $id => $stock)
		{
			if (empty($stock)) $stock = 0;
			$updates[$id]['amount'] = $stock;
		}
	
		// grab comments	
		foreach ($comments as $id => $comment)
		{
			if (!empty($comment))
			{
				$updates[$id]['comments'] = mysql_escape_string($comment);
			}
		}
		
		// update db
		foreach ($updates as $id => $update)
		{
			$amount = $update['amount'];
			$comments = $update['comments'];
			$sql = "INSERT INTO stock(coop_reset_day, product_id, stock_amount, stock_comments) VALUES('$reset_day', '$id', '$amount', '$comments')";
			$this->adapter->query($sql);
		}
		
		$json_reports_model = new Coop_JsonReports;
		$success = $json_reports_model->addWeeklyReportToCoop($coop_id, $reset_day);
	}
}