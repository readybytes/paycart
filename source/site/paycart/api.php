<?php
/**
 * @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package 	PAYCART
 * @contact		suppor+paycart@readybytes.in
 * 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// If file is already included
if ( defined( 'PAYCART_API_DEFINED' ) ) {
	return;	
}

//mark API already loaded
define('PAYCART_API_DEFINED', true);

// load paycart
include_once 'includes.php';


class PaycartAPI
{
	/**
	 * Invoke to get all available published categories
	 * @param INT $parentId :    
	 * 
	 * @return Array of stdclass, which conatin category data
	 */
	static public function getCategories($parentId = null, $lang = null)
	{
		$categoryFilters = array('published' => 1);

		if ( !empty($parentId) ) {
			$categoryFilters['parent_id'] = $parentId;
		}
		$catModel = PaycartFactory::getInstance('productcategory', 'model');
		if($lang == null){
			$lang = paycart_getCurrentLanguage();
			$catModel->lang_code = $lang;
		}
		
		$categories = $catModel->loadRecords($categoryFilters);
		
		$catModel->lang_code = null;
		return $categories;
	}

	/**
	 * Invoke to get current cart
	 * 
	 * @return Paycartcart if cart exits otherwise false
	 */
	static public function getCurrentCart()
	{
		return PaycartFactory::getHelper('cart')->getCurrentCart();
	}

}