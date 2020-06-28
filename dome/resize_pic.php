<?php
use LSYS\FileImageSave;
use LSYS\FileImageSave\Data\DB;
use LSYS\DI\ShareCallback;
include_once __DIR__."/../vendor/autoload.php";
LSYS\Config\File::dirs(array(
    __DIR__."/config",
));

//------------------ 非必须,根据实际需求注册----------------------
LSYS\FileImageSave\DI::set(function (){
    $configs=array(
        "test"=>array(
            "fileimagesave.config.default",
            "fileimagesave.resize.default"
        )
    );
    $db= new DB();
    return (new LSYS\FileImageSave\DI)->fileimagesave(new ShareCallback(function ($config) {
        return $config;
    },function ($config)use($configs,$db){
        if (!isset($configs[$config])){
            throw new Exception("bad config");
        }
        list($save,$resize)=$configs[$config];
        return new FileImageSave(
            \LSYS\Config\DI::get()->config($save),
            \LSYS\Config\DI::get()->config($resize),
            $db
        );
    }));
});
//------------------ 非必须,根据实际需求注册----------------------

$file=isset($_GET['file'])?$_GET['file']:null;
while (strpos($file, "..")!==false){
    $file=str_replace("..", ".", $file);
}
$resize=strstr($file, ".",true);
$file=substr($file, strpos($file, ".")+1);
if (!$file||!$resize){
    http_response_code(404);
    die('not find');
}

$img=LSYS\FileImageSave\DI::get()->fileimagesave("test");
$file=$img->resize($file, $resize);
//var_dump($file);

$status=LSYS\FileGet\DI::get()->fileget()->output($file);
if(!$status)http_response_code(404);
