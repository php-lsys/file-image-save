<?php
namespace LSYS\FileImageSave;
/**
 * @method \LSYS\FileImageSave fileImageSave($config)
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->fileImageSave)&&$di->fileImageSave(new \LSYS\DI\VirtualCallback(\LSYS\FileImageSave::class));
        return $di;
    }
}