<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Front-end
* @contact		support+paycart@readybytes.in 
*/

defined('_JEXEC') or die( 'Restricted access' );

/** 
 * Shipping Rule Helper
 * @since 1.0.0
 * @author Gaurav Jain
 */
class PaycartHelperShippingRule extends PaycartHelper
{	
	/**
	 * Find best shippingrule according to price and grade, in the given list of $shippingrule_list
	 * 
	 * @param array $shippingrule_list list of shipping rule in which best option to be found
	 * @param array $shippingrules_price Once we calculate the price of shipping, put them into this array. so that it can be used later
	 * 
	 * @return array array($best_price_shippingrule, $best_grade_shippingrule, $shippingrules_price)
	 */
	public function getBestRule($shippingrule_list, $product_list, $delivery_address_id, $product_details)
	{
		$best_price = null;
		$best_price_shippingrule = null;
		$best_grade = null;
		$best_grade_shippingrule = null;
		$shippingrules_price = array();

		// Foreach shippingrules of the package, calculate its price, check if it is the best price, position and grade
		foreach ($shippingrule_list as $id_shippingrule){
			$shippingrule_instance = PaycartShippingrule::getInstance($id_shippingrule);

			// TODO : can be combined below two function calls
			list($price_without_tax, $price_with_tax) = $shippingrule_instance->getPackageShippingCost($product_list, $delivery_address_id, $product_details);			
			if (is_null($best_price) || $price_with_tax < $best_price){
				$best_price = $price_with_tax;
				$best_price_shippingrule = $id_shippingrule;
			}

			$shippingrules_price[$id_shippingrule] = array(
					'without_tax' => $price_without_tax,
					'with_tax' => $price_with_tax);

			$grade = $shippingrule_instance->getGrade();
			if (is_null($best_grade) || $grade > $best_grade){
				$best_grade = $grade;
				$best_grade_shippingrule = $id_shippingrule;
			}
		}
				
		return array($best_price_shippingrule, $best_grade_shippingrule, $shippingrules_price);
	}
		
	public function getBestPriceDeliveryOption($packages, $delivery_option_list, $best_price_shippingrules, $shippingrules_price)
	{
		// $delivery_option_list is not being used ,
		// but used to make common for all same function call

		$best_price_shippingrule = array();
		$key = '';

		// Get the delivery option with the lower price
		foreach ($best_price_shippingrules as $id_package => $id_shippingrule)
		{
			$key .= $id_shippingrule.',';
			if (!isset($best_price_shippingrule[$id_shippingrule]))
				$best_price_shippingrule[$id_shippingrule] = array(
					'price_with_tax' => 0,
					'price_without_tax' => 0,
					'package_list' => array(),
					'product_list' => array(),
				);
			$best_price_shippingrule[$id_shippingrule]['price_with_tax'] += $shippingrules_price[$id_package][$id_shippingrule]['with_tax'];
			$best_price_shippingrule[$id_shippingrule]['price_without_tax'] += $shippingrules_price[$id_package][$id_shippingrule]['without_tax'];
			$best_price_shippingrule[$id_shippingrule]['package_list'][] = $id_package;
			$best_price_shippingrule[$id_shippingrule]['product_list'] = array_merge($best_price_shippingrule[$id_shippingrule]['product_list'], $packages[$id_package]['product_list']);				
		}

		// Add the delivery option with best price as best price
		return array($key => array(
				'shippingrule_list' => $best_price_shippingrule,
				'is_best_price' => true,
				'is_best_grade' => false,
				'unique_shippingrule' => (count($best_price_shippingrule) <= 1)
			));			
	}
	
	public function getBestGradeDeliveryOption($packages, $delivery_option_list, $best_grade_shippingrules, $shippingrules_price)
	{
		// Reset $best_grade_shippingrule, it's now an array
			$best_grade_shippingrule = array();
			$key = '';

			// Get the delivery option with the best grade
			foreach ($best_grade_shippingrules as $id_package => $id_shippingrule)
			{
				$key .= $id_shippingrule.',';
				if (!isset($best_grade_shippingrule[$id_shippingrule]))
					$best_grade_shippingrule[$id_shippingrule] = array(
						'price_with_tax' => 0,
						'price_without_tax' => 0,
						'package_list' => array(),
						'product_list' => array(),
					);
				$best_grade_shippingrule[$id_shippingrule]['price_with_tax'] += $shippingrules_price[$id_package][$id_shippingrule]['with_tax'];
				$best_grade_shippingrule[$id_shippingrule]['price_without_tax'] += $shippingrules_price[$id_package][$id_shippingrule]['without_tax'];
				$best_grade_shippingrule[$id_shippingrule]['package_list'][] = $id_package;
				$best_grade_shippingrule[$id_shippingrule]['product_list'] = array_merge($best_grade_shippingrule[$id_shippingrule]['product_list'], $packages[$id_package]['product_list']);
			}
			
			// Add the delivery option with best grade as best grade			
			// already added in option,ist, then do not add it again
			if (!isset($delivery_option_list[$key])){
				$delivery_option_list[$key] = array(
														'shippingrule_list' => $best_grade_shippingrule,
														'is_best_price' => false,					
														'unique_shippingrule' => (count($best_grade_shippingrule) <= 1));
			}		
	
			$delivery_option_list[$key]['is_best_grade'] = true;
			return $delivery_option_list;		
	}
	
	// Common Shippingrule
	public function getUniqueDeliveryOption($packages, $delivery_option_list, $common_shippingrules, $shippingrules_price)
	{
		// Get all delivery options with a unique shippingrule
		foreach ($common_shippingrules as $id_shippingrule)
		{
			$price = 0;
			$key = '';
			$package_list = array();
			$product_list = array();
			$total_price_with_tax = 0;
			$total_price_without_tax = 0;
			$price_with_tax = 0;
			$price_without_tax = 0;

			foreach ($packages as $id_package => $package)
			{
				$key .= $id_shippingrule.',';
				$price_with_tax += $shippingrules_price[$id_package][$id_shippingrule]['with_tax'];
				$price_without_tax += $shippingrules_price[$id_package][$id_shippingrule]['without_tax'];
				$package_list[] = $id_package;
				$product_list = array_merge($product_list, $package['product_list']);
			}

			if (!isset($delivery_option_list[$key]))
				$delivery_option_list[$key] = array(
					'is_best_price' => false,
					'is_best_grade' => false,
					'unique_shippingrule' => true,
					'shippingrule_list' => array(
						$id_shippingrule => array(
							'price_with_tax' => $price_with_tax,
							'price_without_tax' => $price_without_tax,
							'package_list' => $package_list,
							'product_list' => $product_list,
						)
					)
				);
			else
				$delivery_option_list[$key]['unique_shippingrule'] = (count($delivery_option_list[$key]['shippingrule_list']) <= 1);
			}
			
			return $delivery_option_list;
	}

	/**
	 * user defined sorting option to overcome the issue http://stackoverflow.com/questions/20882691/sorting-with-arsort-not-stable
	 * @param array $a
	 * @param array $b
	 */
	public function ruleCounterSort($a, $b)
	{
		return $a[2] == $b[2] ? ($a[0] < $b[0]) : ($a[2] < $b[2] ? 1 : -1);	
	}
	
	public function sortAccordingToCounter($shippingrule_counter)
	{
		//Construct a new array whose elements are the original array's keys, values, and also position
		$temp = array();
		$i = 0;
		foreach ($shippingrule_counter as $key => $value) {
  			$temp[] = array($i, $key, $value);
  			$i++;
		}
				
		// Then sort using a user-defined order that takes the original position into account:
		uasort($temp, array($this, 'ruleCounterSort'));
		
		//Finally, convert it back to the original associative array:
		$shippingrule_counter = array();
		foreach ($temp as $val) {
		  $shippingrule_counter[$val[1]] = $val[2];
		}
				
		return $shippingrule_counter;		
	}
	
	public function getDeliveryOptionList($product_grouped_by_address, $shippingrules_grouped_by_product)
	{	
		$package_list = $this->getPackageList($product_grouped_by_address, $shippingrules_grouped_by_product);
		// Foreach addresses
		foreach ($package_list as $id_address => $packages)
		{
			// Initialize vars
			$delivery_option_list[$id_address] = array();
			$shippingrules_price[$id_address] = array();
			$common_shippingrules = null;
			$best_price_shippingrules = array();
			$best_grade_shippingrules = array();						

			// Foreach packages, get the shippingrules with best price, best position and best grade
			foreach ($packages as $id_package => $package)
			{
				// No shippingrules available
				if (count($package['shippingrule_list']) == 1 && current($package['shippingrule_list']) == 0)
				{	
					$cache = array();
					return $cache;
				}

				$shippingrules_price[$id_address][$id_package] = array();

				// Get all common shippingrules for each packages to the same address
				if (is_null($common_shippingrules))
					$common_shippingrules = $package['shippingrule_list'];
				else
					$common_shippingrules = array_intersect($common_shippingrules, $package['shippingrule_list']);

				$best_price = null;
				$best_price_shippingrule = null;
				$best_grade = null;
				$best_grade_shippingrule = null;

				// get best shipping rule according to price and grade
				list($best_price_shippingrules[$id_package], 
						$best_grade_shippingrules[$id_package],           // IMP : Third argument is for sending details regarding product details
						$shippingrules_price[$id_address][$id_package]) =  $this->getBestRule($package['shippingrule_list'], $package['product_list'], $id_address, $product_grouped_by_address[$id_address]);
			}

			// LIST TYPE 1: Add the delivery option with best price as best price
			$delivery_option_list[$id_address] = $this->getBestPriceDeliveryOption($packages, $delivery_option_list[$id_address], $best_price_shippingrules, $shippingrules_price[$id_address]);
		
			// LIST TYPE 2: Add the delivery option with best price as best grade
			$delivery_option_list[$id_address] = $this->getBestGradeDeliveryOption($packages, $delivery_option_list[$id_address], $best_grade_shippingrules, $shippingrules_price[$id_address]);
				
			// LIST TYPE 3: Get all delivery options with a unique shippingrule
			$delivery_option_list[$id_address] = $this->getUniqueDeliveryOption($packages, $delivery_option_list[$id_address], $common_shippingrules, $shippingrules_price[$id_address]);

		}
		
		// For each delivery options :
		//    - Set the shippingrule list
		//    - Calculate the price
		//    - Calculate the average position
		foreach ($delivery_option_list as $id_address => $delivery_option)
			foreach ($delivery_option as $key => $value)
			{
				$total_price_with_tax = 0;
				$total_price_without_tax = 0;
				$position = 0;
				foreach ($value['shippingrule_list'] as $id_shippingrule => $data)
				{
					$total_price_with_tax += $data['price_with_tax'];
					$total_price_without_tax += $data['price_without_tax'];
			
					$shippingrule_instance = PaycartShippingRule::getInstance($id_shippingrule);					

//					$delivery_option_list[$id_address][$key]['shippingrule_list'][$id_shippingrule]['logo'] = $shippingrule_instance->getLogo(); // TODO : Logo image
					
					$position += $shippingrule_instance->getOrdering();
				}
				$delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
				$delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
				$delivery_option_list[$id_address][$key]['ordering'] = $position / count($value['shippingrule_list']);
			}

		// Sort delivery option list
		foreach ($delivery_option_list as &$array)
			uasort ($array, array($this, 'sortDeliveryOptionList'));
				
		return $delivery_option_list;
	}
	
	/**
	 * 
	 * Sort list of option delivery by parameters define in the BO
	 * VVVVVVVVVVV IMP : Key should be sorted in reverse order if values are same and sorting order is DESC
	 * @param $option1
	 * @param $option2
	 * @return int -1 if $option 1 must be placed before and 1 if the $option1 must be placed after the $option2
	 */
	public function sortDeliveryOptionList($option1, $option2)
	{
		$config = PaycartFactory::getConfig();
		
		if ($config->shippingrule_list_order_by == Paycart::SHIPPINGRULE_LIST_ORDER_BY_PRICE){					
			if ($config->shippingrule_list_order_in == Paycart::SHIPPINGRULE_LIST_ORDER_IN_ASC){
				return ($option1['total_price_with_tax'] <= $option2['total_price_with_tax']) ? -1 : 1 ; // return -1 or 1
			}
			
			//IMP:  sort if in case of equal values (Descending order)
			return ($option1['total_price_with_tax'] > $option2['total_price_with_tax']) ? -1 : 1; // return -1 or 1
		}
		
		// else
		if ($config->shippingrule_list_order_in == Paycart::SHIPPINGRULE_LIST_ORDER_IN_ASC){				// PCTODO : Use constant
			return ($option1['ordering'] <= $option2['ordering']) ? -1 : 1 ; // return -1 or 1
		}

		//IMP:  sort if in case of equal values (Descending order)
		return ($option1['ordering'] > $option2['ordering']) ? -1 : 1 ; // return -1 or 1
	}
	
	public function getPackageList($product_grouped_by_address, $shippingrules_grouped_by_product)
	{	
		$package_list = array();		
		foreach($product_grouped_by_address as $address_id => $products){
			// step1 : get list of shipping rule, applicable for the products to be delivered on this address
			$product_shippingrules = array();
			foreach ($products as $product_id => $product){
				$product_shippingrules[$product_id] = $shippingrules_grouped_by_product[$product_id];
			}		
		
			// step 2 : get shipping rule according to number of occurrence
			$shippingrule_counter = array();  
			foreach($product_shippingrules as $rules){
				foreach($rules as $ruleid){
					if(!isset($shippingrule_counter[$ruleid])){
						$shippingrule_counter[$ruleid] = 0;
					}
					
					$shippingrule_counter[$ruleid]++;
				}
			}
			
			$shippingrule_counter = $this->sortAccordingToCounter($shippingrule_counter);
			
			// step 3 : find minimum number of package
			// loop for each product
			// 		loop for each $shippingrule_counter in decreasing order or occurence
			// 			if shipping rule is applicable for product
			// 				then merge products
			//				and find common shipping rule (if already has some)
			//				break;			
			$package_list[$address_id] = array();		
			foreach($products as $product_id => $product_details){
				$rules = $product_shippingrules[$product_id];
				foreach($shippingrule_counter as $rule => $counter){
					if(!in_array($rule, $rules)){
						continue;
					}
					
					if(!isset($package_list[$address_id][$rule])){
						$package_list[$address_id][$rule]['product_list'] 		= array();
						$package_list[$address_id][$rule]['shippingrule_list'] 	= $rules;					
					}
					
					$package_list[$address_id][$rule]['product_list'] 		= array_values(array_merge($package_list[$address_id][$rule]['product_list'], array($product_id)));
					$package_list[$address_id][$rule]['shippingrule_list'] 	= array_values(array_intersect($package_list[$address_id][$rule]['shippingrule_list'], $rules));
										
					break;
				}
			}
		}
		
		$final_packaging_list = array();
		foreach($package_list as $address_id => $packages){
			$final_packaging_list[$address_id] = array_values($packages);
		}
		
		return $final_packaging_list;
	}
	
}