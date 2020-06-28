<?php
use LSYS\DI\SingletonCallback;
use LSYS\FileImageSave;
use LSYS\FileImageSave\Data\MongoDB;
include_once __DIR__."/../vendor/autoload.php";
LSYS\Config\File::dirs(array(
    __DIR__."/config",
));

//------------------ 非必须,根据实际需求注册----------------------
LSYS\FileGet\DI::set(function (){
    return (new LSYS\FileGet\DI)->fileget(new SingletonCallback(function () {
        return new LSYS\FileGet\GridFS(\LSYS\Config\DI::get()->config("fileget.gridfs"));
    }));
});
\LSYS\FileSave\DI::set(function (){
    $di= new \LSYS\FileSave\DI();
    $di->filesave(new \LSYS\DI\ShareCallback(function($config=null){
        return $config;
    },function($config=null){
        $config=\LSYS\Config\DI::get()->config("filesave.gridfs");
        $save=new \LSYS\FileSave\GridFS($config);
        return $save;
    }));
    return $di;
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
$img=new FileImageSave(
    \LSYS\Config\DI::get()->config("fileimagesave.save.default"),
    \LSYS\Config\DI::get()->config("fileimagesave.resize"),
    new MongoDB()
);


$file=$img->resize($file, $resize);
//var_dump($file);

$file=LSYS\FileGet\DI::get()->fileget()->output($file);
//var_dump($file);