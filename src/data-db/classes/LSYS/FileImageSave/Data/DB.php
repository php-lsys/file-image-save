<?php
namespace LSYS\FileImageSave\Data;
use LSYS\FileImageSave\Data;
use LSYS\Cache;
class DB implements Data{
    /**
     * @var \LSYS\Database
     */
    private $_db;
    private $_cache;
    public function __construct(\LSYS\Database $database=null,Cache $cache=null){
        $this->_cache=$cache;		
        $this->_db=$database?$database:\LSYS\Database\DI::get()->db();
    }
    public function resizeGet(string $file_get_config,?string $file,?string $resize):?string{
        $table=$this->_tableName($file_get_config);
        if(empty($table))return null;
        $file=$this->_db->getConnect()->quote($file);
        $resize=$this->_db->getConnect()->quote($resize);
        $sql="SELECT `resize_file` FROM `{$table}` WHERE `file`={$file} AND `resize`={$resize}";
        $res=$this->_db->getSlaveConnect()->query($sql);
        return $res->get("resize_file");
    }
    public function resizeSet(string $file_get_config,?string $file,?string $resize,?string $resize_file):bool{
        $table=$this->_tableName($file_get_config,true);
        if(empty($table))return false;
        $file=$this->_db->getConnect()->quote($file);
        $resize=$this->_db->getConnect()->quote($resize);
        $resize_file=$this->_db->getConnect()->quote($resize_file);
        $sql="INSERT INTO `{$table}` (`file`, `resize`, `resize_file`) 
            VALUES ({$file}, {$resize}, {$resize_file});";
        RETURN (bool)$this->_db->getMasterConnect()->exec( $sql);
    }
    public function resizeClear(string $file_get_config,?string $file):bool{
		$cache=$this->_cache;
        $table=$this->_tableName($file_get_config);
        if(empty($table))return false;
        $file=$this->_db->getConnect()->quote($file);
        
        $cache_keys=[];
        if ($cache){
            $sql="SELECT `resize` FROM `{$table}` WHERE `file`={$file}";
            $res=$this->_db->getSlaveConnect()->query( $sql);
            foreach ($res as $v){
                $cache_keys[]="image_get".$file_get_config.$file.$v['resize'];
            }
        }
        
        $sql="DELETE FROM {$table} WHERE file={$file}";
        $this->_db->getMasterConnect()->exec($sql);
        if ($cache){
            foreach ($cache_keys as $key)$cache->delete($key);
        }
        return true;
    }
    public function resizeGetAll(string $file_get_config,?string $file):array{
        $table=$this->_tableName($file_get_config);
        if(empty($table))return [];
        $file=$this->_db->getConnect()->quote($file);
        $sql="SELECT `resize_file` FROM `{$table}` WHERE `file`={$file}";
        $res=$this->_db->getSlaveConnect()->query($sql);
        return (array)$res->asArray("resize_file");
    }
    protected function _tableName($file_get_config,$insert=false){
        $tp=$this->_db->tablePrefix();
        $file_get_config=str_replace(".", "_", $file_get_config);
        return "{$tp}imgresize_{$file_get_config}";
    }
}
