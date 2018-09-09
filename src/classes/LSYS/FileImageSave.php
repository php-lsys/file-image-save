<?php
namespace LSYS;
use LSYS\FileImageSave\Data;
use LSYS\FileImageSave\ResizeHandler;
class FileImageSave{
    protected $_config;
    protected $_file_get_config;
    protected $_file_save_config;
    /**
     * @var Data
     */
    protected $_storage;
    protected $_resize_handler;
    public function __construct(Config $config,Config $resize_config,Data $storage){
        $this->_file_get_config=$config->get("fileget");
        $this->_file_save_config=$config->get("filesave");
        $this->_config=$resize_config;
        $this->_storage=$storage;
    }
    /**
     * 重写此方法 实现自定义压缩方式
     * @return ResizeHandler
     */
    protected function get_resize_handler(){
        return new \LSYS\FileImageSave\ResizeHandler\InterventionImage($this->_config);
    }
    /**
     * 移除指定文件的压缩成其他尺寸的图片,不包含本身
     * @param string $file
     */
    public function remove($file){
        $data=$this->_storage->resize_getall($this->_file_get_config,$file);
        $filesave=\LSYS\FileSave\DI::get()->filesave($this->_file_save_config);
        if (is_array($data)){
            foreach ($data as $v) $filesave->remove($v);
        }
        $this->_storage->resize_clear($this->_file_get_config,$file);
    }
    /**
     * 压缩指定文件并返回压缩成功的路径,失败返回false
     * @param string $file
     * @param string $resize
     * @return string|boolean
     */
    public function resize($file,$resize){
        $fconfig=$this->_file_get_config;
        $rfile=$this->_storage->resize_get($fconfig,$file,$resize);
        if ($rfile)return $rfile;
        $fileget=\LSYS\FileGet\DI::get()->fileget($fconfig);
        $lfile=$fileget->download($file);
        if (!$lfile)return false;
        $dir=sys_get_temp_dir();
        $sfile=$dir."/".uniqid();
        $ext=pathinfo($lfile, PATHINFO_EXTENSION);
        if ($ext)$sfile.=".".$ext;
        if (!$this->_resize_handler)$this->_resize_handler=$this->get_resize_handler();
        if (!$this->_resize_handler->resize($lfile,$resize,$sfile))return false;
        $filesave=\LSYS\FileSave\DI::get()->filesave($this->_file_save_config);
        $sfile=$filesave->put($sfile);
        $this->_storage->resize_set($fconfig,$file,$resize,$sfile);
        return $sfile;
    }
}