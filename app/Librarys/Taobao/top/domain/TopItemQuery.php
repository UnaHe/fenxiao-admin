<?php

/**
 * query
 * @author auto create
 */
class TopItemQuery
{
	
	/** 
	 * 页码
	 **/
	public $current_page;
	
	/** 
	 * 一页大小
	 **/
	public $page_size;
	
	/** 
	 * 媒体pid
	 **/
	public $pid;
	
	/** 
	 * 是否包邮
	 **/
	public $postage;
	
	/** 
	 * 状态，预热：1，正在进行中：2
	 **/
	public $status;
	
	/** 
	 * 淘宝类目id
	 **/
	public $taobao_category_id;
	
	/** 
	 * 搜索关键词
	 **/
	public $word;	
}
?>