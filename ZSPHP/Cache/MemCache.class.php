<?php
/**
 * memcache cache
 * @author wangxiaohui
 *
 */

class MemCached
{
	/**
	 * host
	 * @var string
	 */
	protected $_host = '127.0.0.1';
	/**
	 * port
	 * @var string
	 */
	protected $_port = '11211';
	/**
	 * object 对象
	 * @var Redis
	 */
	protected $_object = null;
	/**
	 * 最后一个error
	 * @var string
	 */
	protected $_error = '';


	public function __construct($host = '127.0.0.1', $port = '11211') {
		$this->_host = $host;
		$this->_port = $port;
		$this->_object = new Memcache;
		try {
			$this->_object->addserver($this->_host, $this->_port, 1, 80);
			
		} catch (Exception $e) {
			$this->_error = $e->getMessage();
		}

	}

	/**
	 * 获取缓存数据
	 * @param string $cache_id
	 * @return mixed
	 */
	public function get($cache_id) {
		try {
			$result = $this->_object->get($cache_id);
			if (!$result) {
				return false;
			}
			return $result;
		} catch (Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
	}

	/**
	 * 设置缓存
	 * @param string $cache_id
	 * @param mixed $data 缓存数据
	 * @param int $left 缓存时间 秒
	 * @return bool
	 */
	public function set($cache_id, $data, $left = 60) {
		try {
			
			$retRes = $this->_object->set($cache_id, $data, false, $left);
			
			return $retRes;
		} catch (Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
	}
	
	/**
	*	替换内容
	*	@param string $cache_id
	*	@param mixed $data 缓存数据
	*	@param int $left 缓存时间 秒
	*	@return bool
	*/
	public function replace($cache_id, $data, $left = 60){
		try {
			
			$retRes = $this->_object->replace($cache_id, $data, $left);
			
			return $retRes;
		} catch (Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
	}
	
	/**
	*	添加内容
	*	@param string $cache_id
	*	@param mixed $data 缓存数据
	*	@return bool
	*/
	public function increment($cache_id, $data){
		try {
			
			$retRes = $this->_object->increment($cache_id, $data);
			
			return $retRes;
		} catch (Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
	}
	
	public function __destruct() {
		try {
			if ($this->_object) {
				$this->_object->close();
			}
		} catch (Exception $e) {}
	}
}