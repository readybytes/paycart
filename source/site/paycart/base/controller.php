<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Front-end
* @contact		team@readybytes.in
*/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

/** 
 * Base Controller
* @author Team Readybytes
 */
class PaycartController extends Rb_Controller
{
	public $_component = PAYCART_COMPONENT_NAME;	
	
	/**
	 * Saves an item (new or old)
	 * @TODO:: should be protected.
	 */
	public function _save(array $data, $itemId=null)
	{		
		if($this->__validate($data, $itemId) === false){
			return false;
		}
		
		// if validation is successfull then save the data
		return parent::_save($data, $itemId);
	}
	
	public function save()
	{
		$entity = parent::save();
		// Multilingual : ned to set current url once data is saved (apply)
		// no need to apply is for saveNclose and saveNnew
		if($this->input->get('task', '') == 'apply'){
			$redirect = $this->getRedirect().'&lang_code='.PaycartFactory::getPCCurrentLanguageCode();
			$this->setRedirect($redirect);
		}
		
		return $entity;
	}
	
	public function _populateModelState()
	{
		parent::_populateModelState();
		
		// set the current current language 
		// if it is in url then set it otherwise set joomla's language
		$this->setCurrentLanguage();
	}
	
	public function setCurrentLanguage()
	{
		static $done = false;
		if($done){
			return true;
		}
		
		$app 		= PaycartFactory::getApplication();
		
		//#1 : Get language from URL
		$lang_code 	= $app->input->get('lang_code', '');	
		
		//#2 : If lang_code is empty then get it from post data
		if(empty($lang_code)){
			$post = $this->input->post->get($this->_component->getNameSmall().'_form', array(), 'array');
			if(isset($post['lang_code']) && !empty($post['lang_code'])){
				$lang_code = $post['lang_code'];
			}			
		}
		
		// Error if language is not supported
		$supported_lang = PaycartFactory::getPCSupportedLanguageCode();
		
		if(count($supported_lang) > 1){
			define('PAYCART_MULTILINGUAL', true);
		}
		else{
			define('PAYCART_MULTILINGUAL', false);
		}
		
		if($lang_code && !in_array($lang_code, $supported_lang)){
			// @PCTODO : should throw exception ??
		}
		
		
		//#3 : If lang_code is empty then get Joomla's default language
		if(empty($lang_code)){
			$lang_code = PaycartFactory::getJoomlaCurentLanguageCode();
		}
		
		//#4 : If lang_code is not supported, then get Paycart's default language
		if(!in_array($lang_code, $supported_lang)){
			$lang_code = PaycartFactory::getPCDefaultLanguageCode();
		}
					
		PaycartFactory::setPCCurrentLanguageCode($lang_code);		
		
		// set $done to true, so that it won't be processed more than one time
		$done = true;
	}
}
