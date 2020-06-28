<?php
/**
 * lsys storage
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\FileImageGet\Data\DB;
class MYSQL extends \LSYS\FileImageGet\Data\DB{
    private $_check_cache=array();
    protected function _tableName($file_get_config,$insert=false){
        if (!array_key_exists($file_get_config, $this->_check_cache)) {
            $table=parent::_tableName($file_get_config);
            $table=$this->_db->getConnect()->quote($table);
            $row=$this->_db->getSlaveConnect()->query("SHOW TABLES LIKE {$table};");
            if (!$row->count()){
                if($insert)$this->_tableCreate($table);
                else return null;
            }
            $this->_check_cache[$file_get_config]=$table;
        }
        return $this->_check_cache[$file_get_config];
    }
    protected function _tableCreate($table){
        $sql="CREATE TABLE `{$table}` (
		`file` varchar(255) NOT NULL,
		`resize` varchar(32) NOT NULL,
		`resize_file` varchar(255) NOT NULL,
		PRIMARY KEY( `file`,`resize` )
		) ENGINE=InnoDB;";
        return $this->_db->getMasterConnect()->exec($sql);
    }
}