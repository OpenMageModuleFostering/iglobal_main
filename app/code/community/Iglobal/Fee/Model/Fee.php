<?php
class Iglobal_Fee_Model_Fee extends Varien_Object{

	public static function getFee(){
		return 0;
	}
	public static function canApply($address){
		return true;
		//put here your business logic to check if fee should be applied or not
		$quote = $address->getQuote();
		if($quote->getFeeAmount()){
			return true;
		} else {
			return false;
		}
	}
}