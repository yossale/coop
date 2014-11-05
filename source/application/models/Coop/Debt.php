<?
class Coop_Debt extends Awsome_DbTable
{
	public function AddDebt($user_id, $amount, $date, $comments)
	{
		if ($date != "")
		{
			$explode = explode('/', $date);
			$date = $explode[2] . "-" . $explode[1] . "-" . $explode[0];
		}
		
		$this->adapter->insert("debts", array("user_id" => (int)$user_id, "debt_amount" => (float)$amount,
			"debt_date" => $date, "debt_comments" => $comments));
	}
	
	public function getDebts($coop_id)
	{
		return $this->adapter->fetchAll("SELECT * FROM users u, debts d WHERE d.user_id = u.user_id AND coop_id = $coop_id ORDER BY u.user_first_name");
	}
	
	public function deleteDebt($user_id)
	{
		$this->adapter->query("DELETE FROM debts WHERE user_id = " . (int)$user_id);
	}
}