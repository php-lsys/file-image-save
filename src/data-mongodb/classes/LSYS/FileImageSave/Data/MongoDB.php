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
    public function resizeGet(string $file_get_config,?string $file,?string $resize):?string{
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
    
    public function resizeSet(string $file_get_config,?string $file,?string $resize,?string $resize_file):bool{
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
        return (bool)$insertOneResult->getInsertedId();
    }
    public function resizeClear(string $file_get_config,?string $file):bool{
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $conn->deleteMany([
            'file' => $file,
        ]);
        return true;
    }
    public function resizeGetAll(string $file_get_config,?string $file):array{
        $space=str_replace(".", '_',$file_get_config);
        $conn=$this->_db->selectCollection($space);
        $result = $conn->find([
            'file' => $file,
        ]);
        $out=[];
        foreach ($result->toArray() as $data){
            $out[]=$data->resize_file;
        }
        RETURN $out;
    }
}