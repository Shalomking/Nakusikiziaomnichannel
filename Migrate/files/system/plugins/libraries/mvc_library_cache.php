<?php

class MVC_Library_Cache {

  /**
   * The path to the cache file folder
   *
   * @var string
   */
  private $_cachepath = 'system/storage/cache/';

  /**
   * The name of the default cache file
   *
   * @var string
   */
  private $_cachename = 'default';

  /**
   * The cache file extension
   *
   * @var string
   */
  private $_extension = '.dat';

  /**
   * Default constructor
   *
   * @param string|array [optional] $config
   * @return void
   */
  public function __construct($config = null) {
    if (true === isset($config)) {
      if (is_string($config)) {
        $this->container($config);
      } else if (is_array($config)) {
        $this->container($config['name']);
        $this->setCachePath($config['path']);
        $this->setExtension($config['extension']);
      }
    }
  }

  /**
   * Load appointed cache
   * 
   * @return mixed
   */
  private function _loadCache() {
    if (true === file_exists($this->getCacheDir())) {
      $file = file_get_contents($this->getCacheDir());
      return json_decode($file, true);
    } else {
      return [];
    }
  }

  /**
   * Get the filename hash
   * 
   * @return string
   */
  private function _getHash($filename) {
    return sha1($filename);
  }

  /**
   * Check whether a timestamp is still in the duration 
   * 
   * @param integer $timestamp
   * @param integer $expiration
   * @return boolean
   */
  private function _checkExpired($timestamp, $expiration) {
    $result = false;
    if ($expiration !== 0) {
      $timeDiff = time() - $timestamp;
      ($timeDiff > $expiration) ? $result = true : $result = false;
    }
    return $result;
  }

  /**
   * Check if a writable cache directory exists and if not create a new one
   * 
   * @return boolean
   */
  private function _checkCacheDir() {
    if (!is_dir($this->getCachePath()) && !mkdir($this->getCachePath(), 0775, true)) {
      throw new Exception('Unable to create cache directory ' . $this->getCachePath());
    } elseif (!is_readable($this->getCachePath()) || !is_writable($this->getCachePath())) {
      if (!chmod($this->getCachePath(), 0775)) {
        throw new Exception($this->getCachePath() . ' must be readable and writeable');
      }
    }
    return true;
  }

  /**
   * Erase all expired entries
   * 
   * @return integer
   */
  private function deleteExpired() {
    $cacheData = $this->_loadCache();
    if (true === is_array($cacheData)) {
      $counter = 0;
      foreach ($cacheData as $key => $entry) {
        if (true === $this->_checkExpired($entry['time'], $entry['expire'])) {
          unset($cacheData[$key]);
          $counter++;
        }
      }
      if ($counter > 0) {
        $cacheData = json_encode($cacheData);
        file_put_contents($this->getCacheDir(), $cacheData);
      }
      return $counter;
    }
  }

  /**
   * Get the cache directory path
   * 
   * @return string
   */
  public function getCacheDir() {
    if (true === $this->_checkCacheDir()) {
      $filename = $this->getCache();
      $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
      return $this->getCachePath() . $this->_getHash($filename) . $this->getExtension();
    }
  }

  /**
   * Cache path Setter
   * 
   * @param string $path
   * @return object
   */
  public function setCachePath($path) {
    $this->_cachepath = $path;
    return $this;
  }

  /**
   * Cache path Getter
   * 
   * @return string
   */
  public function getCachePath() {
    return $this->_cachepath;
  }

  /**
   * Cache name Getter
   * 
   * @return void
   */
  public function getCache() {
    return $this->_cachename;
  }

  /**
   * Cache file extension Setter
   * 
   * @param string $ext
   * @return object
   */
  public function setExtension($ext) {
    $this->_extension = $ext;
    return $this;
  }

  /**
   * Cache file extension Getter
   * 
   * @return string
   */
  public function getExtension() {
    return $this->_extension;
  }

  /**
   * Cache name Setter
   * 
   * @param string $name
   * @return object
   */
  public function container($name, $deleteExpired = false) {
    $this->_cachename = $name;

    if($deleteExpired):
      $this->deleteExpired();
    endif;

    return $this;
  }

  /**
   * Check whether data accociated with a key
   *
   * @param string $key
   * @return boolean
   */
  public function has($key) {
    return isset($this->_loadCache()[$key]["data"]);
  }

  /**
   * Check if container is empty
   *
   * @param string $key
   * @return boolean
   */
  public function empty() {
    return empty($this->_loadCache());
  }

  /**
   * Check if container exist
   * 
   * @return object
   */
  public function exist() {
    return file_exists($this->getCacheDir());
  }

  /**
   * Store data in the cache
   *
   * @param string $key
   * @param mixed $data
   * @param integer [optional] $expiration
   * @return object
   */
  public function set($key, $data, $expiration = 0) {
    $storeData = [
      "time" => time(),
      "expire" => $expiration,
      "data" => serialize($data)
    ];
    $dataArray = $this->_loadCache();
    if (true === is_array($dataArray)) {
      $dataArray[$key] = $storeData;
    } else {
      $dataArray = [$key => $storeData];
    }
    $cacheData = json_encode($dataArray);
    return file_put_contents($this->getCacheDir(), $cacheData) ? true : false;
  }

  /**
   * Store raw data in the cache
   *
   * @param string $raw
   * @return bool
   */
  public function setRaw($raw) {
    $dataArray = $this->_loadCache();
    $dataArray["raw"] = $raw;
    $cacheData = json_encode($dataArray);
    return file_put_contents($this->getCacheDir(), $cacheData) ? true : false;
  }


  /**
   * Store data in the cache as array
   *
   * @param string $key
   * @param mixed $data
   * @param integer [optional] $expiration
   * @return object
   */
  public function setArray($array, $expiration = 0) {
    $dataArray = $this->_loadCache();
    foreach($array as $key => $value):
      $dataArray[$key] = [
        "time" => time(),
        "expire" => $expiration,
        "data" => serialize($value)
      ];
    endforeach;
    $cacheData = json_encode($dataArray);
    return file_put_contents($this->getCacheDir(), $cacheData) ? true : false;
  }

  /**
   * Retrieve cached data by its key
   * 
   * @param string $key
   * @param boolean [optional] $timestamp
   * @return string
   */
  public function get($key, $timestamp = false) {
    $cachedData = $this->_loadCache();
    (false === $timestamp) ? $type = 'data' : $type = 'time';
    if (!isset($cachedData[$key][$type])) return null; 
    return unserialize($cachedData[$key][$type]);
  }

  /**
   * Retrieve cached raw data
   * 
   * @param string $key
   * @param boolean [optional] $timestamp
   * @return string
   */
  public function getRaw() {
    $cachedData = $this->_loadCache();
    if (empty($cachedData)) return null; 
    return $cachedData["raw"];
  }

  /**
   * Retrieve all cached data
   * 
   * @param boolean [optional] $meta
   * @return array
   */
  public function getAll($meta = false) {
    if ($meta === false) {
      $results = [];
      $cachedData = $this->_loadCache();
      if ($cachedData) {
        foreach ($cachedData as $k => $v) {
          $results[$k] = unserialize($v['data']);
        }
      }
      return $results;
    } else {
      return $this->_loadCache();
    }
  }

  /**
   * Erase cached entry by its key
   * 
   * @param string $key
   * @return object
   */
  public function delete($key) {
    $cacheData = $this->_loadCache();
    if (true === is_array($cacheData)) {
      if (true === isset($cacheData[$key])) {
        unset($cacheData[$key]);
        $cacheData = json_encode($cacheData);
        file_put_contents($this->getCacheDir(), $cacheData);
      } else {
        return false;
      }
    }
    return $this;
  }

  /**
   * Erase all cached entries
   * 
   * @return object
   */
  public function clear() {
    $cacheDir = $this->getCacheDir();
    
    if (true === file_exists($cacheDir))
      @unlink($cacheDir);
    
    return $this;
  }
}