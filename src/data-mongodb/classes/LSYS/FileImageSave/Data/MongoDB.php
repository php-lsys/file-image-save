<?php
namespace LSYS\FileImageSave\Data;
class MongoDB implements \LSYS\FileImageSave\Data {
    /**
     * @var \LSYS\MongoDB
     */
    private $_db;
    public function __construct(\LSYS\MongoDB $monggodb=null){
        $monggodb=$monggodb?$monggodb:\LSYS\MongoDB\DI::get()->mongodb();
        $this->_db = $monggodb->getDatabase();
    }
    public function resizeGet($file_get_config,$file,$resize){
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $result = $conn->find([
            'file' => $file,
            'resize' => $resize,
        ]);
        $data=current($result->toArray());
        if (!$data)return NULL;
        RETURN $data->resize_file;
    }
    
    public function resizeSet($file_get_config,$file,$resize,$resize_file){
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $conn->deleteOne([
            'file' => $file,
            'resize' => $resize
        ]);
        $insertOneResult = $conn->insertOne([
            'file' => $file,
            'resize' => $resize,
            'resize_file' => $resize_file,
        ]);
        return $insertOneResult->getInsertedId();
    }
    public function resizeClear($file_get_config,$file){
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $conn->deleteMany([
            'file' => $file,
        ]);
        return true;
    }
    public function resizeGetAll($file_get_config,$file){
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $result = $conn->find([
            'file' => $file,
            'resize' => $resize,
        ]);
        $out=[];
        foreach ($result->toArray() as $v){
            $out[]=$data->resize_file;
        }
        RETURN $out;
    }
}