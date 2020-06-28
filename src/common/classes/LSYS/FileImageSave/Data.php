<?php
namespace LSYS\FileImageSave;
interface Data{
    /**
     * 获取指定压缩文件的数据
     * @param string $file_get_config
     * @param string $file
     * @param string $resize
     */
    public function resizeGet(string $file_get_config,?string $file,?string $resize):?string;
    /**
     * 添加指定压缩的关键数据存储
     * @param string $file_get_config
     * @param string $file
     * @param string $resize
     * @param string $resize_file
     */
    public function resizeSet(string $file_get_config,?string $file,?string $resize,?string $resize_file):bool;
    /**
     * 清理指定压缩文件的关系数据存储
     * @param string $file_get_config
     * @param string $file
     */
    public function resizeClear(string $file_get_config,?string $file):bool;
    /**
     * 返回指定文件已存在的所有压缩文件
     * @param string $file_get_config
     * @param string $file
     */
    public function resizeGetAll(string $file_get_config,?string $file):array;
}