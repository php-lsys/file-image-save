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
    public function resizeGet($file_get_config,$file,$resize){
        $table=$this->_tableName($file_get_config);
        if(empty($table))return null;
        $file=$this->_db->quote($file);
        $resize=$this->_db->quote($resize);
        $sql="SELECT `resize_file` FROM `{$table}` WHERE `file`={$file} AND `resize`={$resize}";
        $res=$this->_db->query($sql);
        return $res->get("resize_file");
    }
    public function resizeSet($file_get_config,$file,$resize,$resize_file){
        $table=$this->_tableName($file_get_config,true);
        if(empty($table))return null;
        $file=$this->_db->quote($file);
        $resize=$this->_db->quote($resize);
        $resize_file=$this->_db->quote($resize_file);
        $sql="INSERT INTO `{$table}` (`file`, `resize`, `resize_file`) 
            VALUES ({$file}, {$resize}, {$resize_file});";
        RETURN $this->_db->exec( $sql);
    }
    public function resizeClear($file_get_config,$file){
		$cache=$this->_cache;
        $table=$this->_tableName($file_get_config);
        if(empty($table))return null;
        $file=$this->_db->quote($file);
        
        $cache_keys=[];
        if ($cache){
            $sql="SELECT `resize` FROM `{$table}` WHERE `file`={$file}";
            $res=$this->_db->query( $sql);
            foreach ($res as $v){
                $cache_keys[]="image_get".$file_get_config.$file.$v['resize'];
            }
        }
        
        $sql="DELETE FROM {$table} WHERE file={$file}";
        $this->_db->exec($sql);
        if ($cache){
            foreach ($cache_keys as $key)$cache->delete($key);
        }
        return true;
    }
    public function resizeGetAll($file_get_config,$file){
        $table=$this->_tableName($file_get_config);
        if(empty($table))return null;
        $file=$this->_db->quote($file);
        $sql="SELECT `resize_file` FROM `{$table}` WHERE `file`={$file}";
        $res=$this->_db->query($sql);
        return $res->asArray("resize_file");
    }
    protected function _tableName($file_get_config,$insert=false){
        $tp=$this->_db->tablePrefix();
        $file_get_config=str_replace(".", "_", $file_get_config);
        return "{$tp}imgresize_{$file_get_config}";
    }
}
