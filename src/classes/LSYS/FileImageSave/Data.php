<?php
namespace LSYS\FileImageSave;
interface Data{
    /**
     * 获取指定压缩文件的数据
     * @param string $file_get_config
     * @param string $file
     * @param string $resize
     */
    public function resizeGet($file_get_config,$file,$resize);
    /**
     * 添加指定压缩的关键数据存储
     * @param string $file_get_config
     * @param string $file
     * @param string $resize
     * @param string $resize_file
     */
    public function resizeSet($file_get_config,$file,$resize,$resize_file);
    /**
     * 清理指定压缩文件的关系数据存储
     * @param string $file_get_config
     * @param string $file
     */
    public function resizeClear($file_get_config,$file);
    /**
     * 返回指定文件已存在的所有压缩文件
     * @param string $file_get_config
     * @param string $file
     */
    public function resizeGetAll($file_get_config,$file);
}