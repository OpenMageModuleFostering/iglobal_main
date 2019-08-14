<?php
class Iglobal_Fee_Model_Sales_Order_Total_Invoice_Fee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();
		$feeAmountLeft = $order->getFeeAmount() - $order->getFeeAmountInvoiced();
		$baseFeeAmountLeft = $order->getBaseFeeAmount() - $order->getBaseFeeAmountInvoiced();
			
		$invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmountLeft);
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseFeeAmountLeft);

		$invoice->setFeeAmount($feeAmountLeft);
		$invoice->setBaseFeeAmount($baseFeeAmountLeft);
		return $this;
	}
}
