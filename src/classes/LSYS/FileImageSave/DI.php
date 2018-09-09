<?php
namespace LSYS\FileImageSave;
/**
 * @method \LSYS\FileImageSave fileimagesave($config)
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->fileimagesave)&&$di->fileimagesave(new \LSYS\DI\VirtualCallback(\LSYS\FileImageSave::class));
        return $di;
    }
}