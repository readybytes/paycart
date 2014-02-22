<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Back-end
* @contact		support+paycart@readybytes.in 
*/
defined('_JEXEC') or die( 'Restricted access' );
/**
 * Admin Base View for Shipping Rules
 * 
 * @since 1.0.0
 *  
 * @author Gaurav Jain
 */
class PaycartAdminBaseViewGroup extends PaycartView
{	
	/**
	 * @var PaycartHelperGroup
	 */
	protected $_helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->_helper = PaycartFactory::getHelper('group');
	}
}