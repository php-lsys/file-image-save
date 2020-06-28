<?php
namespace LSYS\FileImageSave;
interface ResizeHandler{
    /**
     * 压缩指定图片文件并保存到指定路径
     * @param string $file
     * @param string $resize
     * @param string $save_file
     * @return bool
     */
    public function resize($file,$resize,$save_file);
}