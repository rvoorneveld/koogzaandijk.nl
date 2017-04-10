<?php
class KZ_Models_Blog extends KZ_Controller_Table
{

	protected $_name = 'blog';
	protected $_primary = 'id';

	public function getBlog()
	{
		$strQuery = $this->select()
					->setIntegrityCheck(false)
					->from('blog_item')
					->joinLeft('blog','blog_item.blog_id = blog.id',['name']);
		return $this->returnData($strQuery);
	}

}