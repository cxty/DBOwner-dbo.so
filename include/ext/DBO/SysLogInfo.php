<?php
/**
 * SysLogInfo操作类
 * @author Cxty
 *
 */
class SysLogInfo {
	public $model; //数据库模型对象
	public $config; //全局配置
	
	public function __construct($_model,$_config) {
		if(!isset($this->model)){
			$this->model=$_model;
		}
		if(!isset($this->config)){
			$this->config=$_config;
		}
	}
	
	/**
	 * 取单个记录
	 * @param int $SysLogID
	 */
	public  function Get($SysLogID)
	{
		try
		{
		$condition = array();
		$condition['SysLogID'] = $SysLogID;
		return $this->model
		->table('tbSysLogInfo',false)
		->field('SysLogID,
				slActType,
				slActParameter,
				slReCallValue,
				slAppendTime
				')
				->where($condition)
				->find();
		}
		catch(Exception  $e)
		{
			return null;
		}
	}
	
	/**
	 * 取多个记录
	 * @param string/array $condition
	 */
	public  function GetList($condition)
	{
		try
		{
			return $this->model
			->table('tbSysLogInfo',false)
			->field('SysLogID,
					slActType,
					slActParameter,
					slReCallValue,
					slAppendTime
					')
					->where($condition)
					->select();
		}
		catch(Exception  $e)
		{
			return null;
		}
	}
	
	/**
	 * 分页查询
	 * @param string/array $condition
	 * @param string $order
	 * @param int $pagesize
	 * @param int $page
	 */
	public  function GetListForPage($condition,$order,$pagesize,$page)
	{
		try{
			$limit_start=($page-1)*$pagesize;
			$limit=$limit_start.','.$pagesize;
		
			//获取行数
			$count=$this->model->table('tbSysLogInfo',false)->field('SysLogID')->where($condition)->count();
			
			$list=$this->model->table('tbSysLogInfo',false)
			->field('SysLogID,
					slActType,
					slActParameter,
					slReCallValue,
					slAppendTime				
					')->where($condition)->order($order)->limit($limit)->select();

			return array('count'=>$count,'list'=>$list);
		}
		catch(Exception  $e)
		{
			return array('count'=>0,'list'=>null);
		}
	}
	
	/**
	 * 添加一条记录
	 * @param unknown_type $slActType
	 * @param unknown_type $slActParameter
	 * @param unknown_type $slReCallValue
	 * @param unknown_type $slAppendTime
	 */
	public  function Insert($slActType,$slActParameter,$slReCallValue,$slAppendTime)
	{
		try
		{
			$data = array();
			$data['slActType']=$slActType;
			$data['slActParameter']=$slActParameter;
			$data['slReCallValue']=$slReCallValue;
			$data['slAppendTime']=$slAppendTime;
		
			return $this->model->table('tbSysLogInfo',false)->data($data)->insert();
		}
		catch(Exception  $e)
		{
			return null;
		}
	}
	
	/**
	 * 更新记录
	 * @param unknown_type $SysLogID
	 * @param unknown_type $slActType
	 * @param unknown_type $slActParameter
	 * @param unknown_type $slReCallValue
	 */
	public  function Update($SysLogID,$slActType,$slActParameter,$slReCallValue)
	{
		try
		{
			$condition = array();
			$data = array();
			$condition['SysLogID'] = $SysLogID;
		
			$data['slActType']=$slActType;
			$data['slActParameter']=$slActParameter;
			$data['slReCallValue']=$slReCallValue;
		
			return $this->model->table('tbSysLogInfo',false)->data($data)->where($condition)->update();
		}
		catch(Exception  $e)
		{
			return null;
		}
	}
	
	/**
	 * 删除记录
	 * @param string/array $condition
	 */
	public  function Delete($condition)
	{
		try{
			return $this->model->table('tbSysLogInfo',false)->where($condition)->delete();
		}
		catch(Exception  $e)
		{
			return null;
		}
	}
}

?>