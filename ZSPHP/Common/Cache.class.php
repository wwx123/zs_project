<?php
/**
 * Cache.class.php
 *
 * 紫色医疗框架全局共用方法
 * 
 * @author WangXiaohui<wangxiaohui0312@gmail.com>
 */

include_once ZS_PHP_PATH . '/Cache/RedisCache.class.php';
include_once ZS_PHP_PATH . '/Cache/MemCache.class.php';

class Cache
{
	public static function init($type = 'Redis'){
		
		if('Redis' == $type){
			$host = C('REDIS_HOST');
			$port = C('REDIS_PORT');
			$pwd = C('REDIS_PWD');
			
			if(!$host || !$port){
				myExit('No Redis Config');
			}
			
			return new RedisCache($host, $port, $pwd);
		}
		
		if('Memcached' == $type){
			$host = C('MEMCACHED_HOST');
			$port = C('MEMCACHED_PORT');
			
			if(!$host || !$port){
				myExit('No Memcached Config');
			}
			
			return new MemCached($host, $port);
		}
		
		//TODO 其他缓存方式待扩展
	}
}
?>