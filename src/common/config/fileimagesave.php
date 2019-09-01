<?php
/**
 * lsys storage
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

return array(
    "config"=>array(
        "default"=>array(//
            "fileget"=>'fileget.default',
            "filesave"=>'filesave.default',
        ),
    ),
    "resize"=>array(
        "default"=>array(//
            'pic_10'=>array(
                'width'=>10,
                'height'=>10,
                //'master'=>\LSYS\FileImageSave\ResizeHandler\InterventionImage::FILL,
            ),
            'pic_20'=>array(
                'width'=>20,
                'height'=>20,
            ),
        ),
    )
);