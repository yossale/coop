<?
class Coop_WeeklyReports {

    public function __construct()
    {
        $this->logger = Zend_Registry::get('logger');
    }

	public function getWeeklyReport($coop_id, $reset_day)
	{
        $this->logger->info("Starting to generate weekly report for coop #$coop_id on reset day $reset_day");

		$coop_orders = new Coop_Orders;
		$coop_prices = new Coop_Prices;

		$weekly_report = $this->getOrdersSummary($reset_day, $coop_id);
		$stock_and_comments = $this->getStockAndComments($reset_day);
		$supplier_and_user_payments = $this->getSupplierAndUserPaymentSummary($weekly_report, $stock_and_comments['stock']);				
        $payments = $coop_orders->getPayments($coop_id, $reset_day);		
		$prices = $coop_prices->getPrices($coop_id, $reset_day);
		
		$returned = array('report' => $weekly_report,
						  'stock' => $stock_and_comments['stock'],
						  'comments' => $stock_and_comments['comments'],
						  'sum' => $supplier_and_user_payments['supplierPaymentSum'],
						  'user_payments' => $supplier_and_user_payments['userPaymentSum'],
						  'payments' => $payments,
						  'prices' => $prices);

        $this->logger->info("Done generating weekly report");

		return $returned;
	}

	private function getOrdersSummary($reset_day, $coop_id)
	{
		$coop_orders = new Coop_Orders();
		$weekly_report = $coop_orders->getWeekSummary($reset_day, $coop_id);
		
		if ($weekly_report != null)
		{
			// get orders for each product
			foreach ($weekly_report as $key => $product)
			{
				$orders = $coop_orders->getOrdersOfProduct($reset_day, $product['product_id']);
				if ($orders)
				{
					$weekly_report[$key]['orders'] = $orders;
				}
	
				$weekly_report[$key]['orders_count'] = (int)$coop_orders->countWeeklyOrdersForProduct($coop_id, $product['product_id'], $reset_day);
			}
			
		}
		return $weekly_report;
	}
	
	private function getStockAndComments($reset_day) 
	{
		$coop_stock = new Coop_Stock;
		 
		$stockResults = $coop_stock->getStock($reset_day);
		$stock = array();
		$comments = array();
		if ($stockResults)
		{
			foreach ($stockResults as $row)
			{
				$stock[$row['product_id']] = $row['stock_amount'];
				$comments[$row['product_id']] = $row['stock_comments'];
			}
		}
		
		return array('stock' => $stock, 'comments' => $comments);
	}
	
	private function getSupplierAndUserPaymentSummary($weekly_report, $stock)
	{
		if (!empty($weekly_report))
		{
			$currentCategory = 0;
			$supplierPayment = array();
			$supplierPaymentSum = array();
			
			$userPayment = array();
			$userPaymentSum = array();
			
			foreach ($weekly_report as $product)
			{
				$supplierPayment[$product['category_id']][] = $product['product_coop_cost'] * $stock[$product['product_id']];
				
				$userPayment[$product['category_id']][] = $product['product_price'] * $stock[$product['product_id']];
			}
			
			foreach ($supplierPayment as $category_id => $amounts)
			{
				$supplierPaymentSum[$category_id] = array_sum($amounts);
			}

			foreach ($userPayment as $category_id => $amounts)
			{
				$userPaymentSum[$category_id] = array_sum($amounts);
			}
			
			return array('supplierPaymentSum' => $supplierPaymentSum, 'userPaymentSum' => $userPaymentSum);
		}
	}
	
}
