<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Back-end
* @contact		team@readybytes.in
* @author 		Puneet Singhal 
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Cart Model
 */
class PaycartModelCart extends PaycartModel
{
	var $filterMatchOpeartor = Array(
									'buyer_id' 	=> array('='),
									'status'	=> array('=')
									);
}

class PaycartModelformCart extends PaycartModelform { }

/** 
 * Cart Table
 */
class PaycartTableCart extends PaycartTable {}