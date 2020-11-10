<?php
class Config implements \ArrayAccess
{


    static private $config;

    static private $_configPath;

    private $configarray;


    private function __construct() {
		$defaultDir = __DIR__ . '/';//该默认路径应该通过配置常量定义来获取的
		self::$_configPath = !empty( $configPath ) && is_dir( $configPath ) ? $configPath : $defaultDir;
		$dir = new \RecursiveDirectoryIterator(self::$_configPath); //获取路径下的所有文件和目录，不会递归输出
		$objects = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

		foreach( $objects as $fileName => $object ){
			// var_dump($object->fileName);
			// var_dump($object->getBasename());
			$base_name = $object->getBasename();
			if ($base_name == '.' || $base_name == '..') {
				continue;
			}
			if ($base_name == 'Config.class.php') {
				continue;
			}
			$pathInfo = pathinfo($fileName);
			$this->configarray[$pathInfo['filename']] = include $fileName;

		}

	}



	public static function getInstance() {

		if (self::$config == null){

			self::$config = new self;

		}

		return self::$config;

	}


	//检查一个偏移位置是否存在

	function offsetExists($index) {

		return isset($this->configarray[$index]);

	}

	//获取一个偏移位置的值
 	function offsetGet($index) {

		return $this->configarray[$index];

	}

	//设置一个偏移位置的值

	function offsetSet($index, $newvalue) {

		$this->configarray[$index] = $newvalue;

	}

	//复位一个偏移位置的值

	function offsetUnset($index) {

	 unset($this->configarray[$index]);

	}
}

function config($key){
	try {
		$config = Config::getInstance();
		$path = $key;
		if (strpos($key, '.') !== false){
			$path = explode('.', $key);
		}
		if (is_string($path)){
			if (!isset($config[$path])) {
				throw new \Exception($path . ' 配置文件不存在！');
			}
			return $config[$path];
		}
		$len = count($path);
		if (!isset($config[$path[0]])) {
			throw new \Exception($path[0] . ' 配置文件不存在！');
		}
		$i = 1;
		$res = $config[$path[0]];
		while ($i < $len) {
			// $var_str .= '[$path[' . $i . ']]';
			if (isset($res[$path[$i]])) {
				$res = $res[$path[$i]];
			} else {
				$res = null;
				break;
			}
			++$i;
		}
		return $res;
	} catch(\Exception $e){
		echo $e->getMessage();
	}
}