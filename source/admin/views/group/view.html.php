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
 * Admin Html View for Group
 * 
 * @since 1.0.0
 *  
 * @author Gaurav Jain
 */
require_once dirname(__FILE__).'/view.php';
class PaycartAdminViewGroup extends PaycartAdminBaseViewGroup
{	
	public function display($tpl=null) 
	{
		$availiableGroupRules = $this->_helper->getList();	
		$this->assign('availiableGroupRules', $availiableGroupRules);
		
		return parent::display($tpl);
	}
	
	public function edit($tpl=null) 
	{
		$itemId			=  $this->getModel()->getId();
		$item			=  PaycartGroup::getInstance($itemId);
		$rulehtml 		= '';
		$ruleScripts  	= array();
		$ruleCounter 	= 0;
		
		if(!$itemId){
			$type = $this->input->get('type', '');
			if(empty($type)){
				throw new Exception(JText::_('COM_PAYCART_ERROR_GROUP_TYPE_ARGUMENT_MISSING'), 404);			
			}

			$item->bind(array('type' => $type));
		}
		else{
			$type 		= $item->getType();			
			$params 	= $item->getParams();			
			  
			foreach($params as $rule){
				$namePrefix = $this->_component->getNameSmall().'_form[params]['.$ruleCounter.']';
				
				// get instance of rule
				$groupRule = PaycartFactory::getGrouprule($type, $rule->ruleClass, (array)$rule);
				$result = $groupRule->getParamsHtml($namePrefix);
				$paramsHtml = '';
				$scripts 	= '';
				if(!is_array($result)){
					$paramsHtml = $result;
				}
				else{
					$paramsHtml = array_shift($result);
					// if is is still array
					if(is_array($result))
					$scripts = array_shift($result);
				}
		
				$this->assign('paramsHtml', $paramsHtml);
				$this->assign('namePrefix', $namePrefix);
				$this->assign('ruleClass',  $rule->ruleClass);
				$this->assign('ruleType',   $type);	
				
				$rulehtml .= $this->loadTemplate('rule_params');
				$ruleScripts = array_merge($ruleScripts, $scripts);	
				$ruleCounter++;
			}
		}

		$this->assign('ruleHtml', $rulehtml);
		$this->assign('ruleScripts', $ruleScripts);
		$this->assign('ruleCounter', $ruleCounter);
		
		$availableGroupRules = $this->_helper->getList();
		if(!isset($availableGroupRules[$type])){
			throw new Exception(JText::_('COM_PAYCART_ERROR_GROUP_TYPE_ARGUMENT_MISSING'), 404);			
		}
		
		$groupRules = $availableGroupRules[$type];		
		
		$this->assign('form',  $item->getModelform()->getForm($item));
		$this->assign('group_rules', $groupRules); 
		return parent::edit($tpl);
	}
}