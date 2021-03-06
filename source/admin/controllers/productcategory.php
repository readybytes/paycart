<?php

/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYCART
* @subpackage	Back-end
* @contact		team@readybytes.in
* @author		Manish Trivedi
*/

// no direct access
defined( '_JEXEC' ) or	die( 'Restricted access' );

/** 
 * Category Controller
 * @author Manish Trivedi
 */

class PaycartAdminControllerProductcategory extends PaycartController {
	
	/**
	 * 
	 * Ajax Call : To create new category
	 */
	function create()
	{
		//Check Joomla Session user should be login
		if ( !JSession::checkToken() ) {
			//@PCTODO :: Rise exception 
		}
		
		$post['title'] = $this->input->get('category_name',null,'RAW');
		
		if (!$post['title']) {
			// @codeCoverageIgnoreStart
			throw new UnexpectedValueException(Rb_Text::sprintf('COM_PAYCART_INVALID_POST_DATA', '$title must be required'));
			// @codeCoverageIgnoreEnd
		}
		
		$category = $this->_save($post);
		// Id required in View
		// IMP:: don't put category_id in property name otherwise it will not work 
		$this->getModel()->setState('id', $category->getId());
		
		return  true;
	}
	
	/**
	 * override it due to get all uploaded files 
	 */
	public function _save(array $data, $itemId=null, $type=null)
	{
		//Get All files from paycart form
		$data['_uploaded_files'] = $this->input->files->get($this->getControlNamePrefix(), false);	
		
		$entity = parent::_save($data, $itemId, $type);
		
		//Issue #415 required to add the current category at end 
		//otherwise redirection will be on the last category in array 
		//because itemid will be fetched from table object of last saved record
		if($entity instanceof Rb_Lib && $entity->getId()){
			$this->getModel()->getTable()->load($entity->getId());
		}
		
		return $entity;
	}	

	/**
	 * Ajax task : Delete image attached to productCategory
	 */
	public function deleteImage()
	{
		$id = $this->input->get('productCategory_id',0);
		$instance  = PaycartProductcategory::getInstance($id);
				 
		$ret = $instance->deleteImage($instance->getCoverMedia(false));
		
		$view = $this->getView();
		if($ret){
			$instance->save();
			$view->assign('success', true);
		}
		else{
			$view->assign('success', false);
		}
	
		return true;
	}
	
	/**
	 * Overriding it because in tablenested children will automatically get deleted 
	 * when try to delete parent category
	 * So here we check if record exist in table then only try to delete the record
	 *  
	 * (non-PHPdoc)
	 * @see plugins/system/rbsl/rb/rb/Rb_Controller::_remove()
	 */
	function _remove($itemId=null, $userId=null)
	{
		//get the model
		$model 		= $this->getModel();
	    if($itemId === null || $itemId === 0){
			$itemId = $model->getId();
		}
		
		//check if record exists
		if($model->getTable()->load($itemId)){
			$item = Rb_Lib::getInstance($this->_component->getPrefixClass(), $this->getName(), $itemId, null)
					->delete();
	
			if(!$item){
				//we need to set error message
				$this->setError($model->getError());
				return false;
			}
		}
		
		return true;
	}
}
