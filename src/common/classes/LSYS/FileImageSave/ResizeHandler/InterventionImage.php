<?php
/**
 * lsys storage
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\FileImageSave\ResizeHandler;
use LSYS\FileImageSave\ResizeHandler;
use LSYS\Config;
class InterventionImage implements ResizeHandler{
    // Resizing contraints
    const NONE    = 0x01;
    const WIDTH   = 0x02;
    const HEIGHT  = 0x03;
    const AUTO    = 0x04;
    const INVERSE = 0x05;
    const REMOVE  = 0x06;
    const FILL    = 0x07;
    const TOP_REMOVE=0x08;
    
    protected  $_config;
    
    protected  $_image_mgr;
    /**
     * Loads information about the image. Will throw an exception if the image
     * does not exist or is not an image.
     *
     * @param   string   image file path
     * @return  void
     */
    public function __construct(Config $config)
    {
        $this->_config=$config;
        $this->_image_mgr=new \Intervention\Image\ImageManager();
    }
    /**
     * Resize the image to the given size. Either the width or the height can
     * be omitted and the image will be resized proportionally.
     *
     *     // Resize to 200 pixels on the shortest side
     *     $image->resize(200, 200);
     *
     *     // Resize to 200x200 pixels, keeping aspect ratio
     *     $image->resize(200, 200, self::INVERSE);
     *
     *     // Resize to 500 pixel width, keeping aspect ratio
     *     $image->resize(500, NULL);
     *
     *     // Resize to 500 pixel height, keeping aspect ratio
     *     $image->resize(NULL, 500);
     *
     *     // Resize to 200x500 pixels, ignoring aspect ratio
     *     $image->resize(200, 500, self::NONE);
     *
     * @param   integer  new width
     * @param   integer  new height
     * @param   integer  master dimension
     * @return  $this
     * @uses    Image::_do_resize
     */
    
    protected function _resize($thisimage,$width,$height,$master){
        $thiswidth=$thisimage->width();
        $thisheight=$thisimage->height();
        if ($master==self::TOP_REMOVE){
            if ($thiswidth/$width>$thisheight/$height){
                $this->_resize($thisimage,$width,$height,self::HEIGHT);
                $thisimage->crop($width, $height,0,0);
            }else{
                $this->_resize($thisimage,$width,$height,self::WIDTH);
                $offy=(int)($thisheight-$height)/2;
                $thisimage->crop($width, $height,0,$offy);
            }
            return $this;
        }
        if ($master==self::REMOVE){
            if ($thiswidth/$width>$thisheight/$height){
                $this->_resize($thisimage,$width,$height,self::HEIGHT);
                $offx=(int)($thiswidth-$width)/2;
                $thisimage->crop($width, $height,$offx,0);
            }else{
                $this->_resize($thisimage,$width,$height,self::WIDTH);
                $offy=(int)($thisheight-$height)/2;
                $thisimage->crop($width, $height,0,$offy);
            }
            return $this;
        }
        
        if ($master==self::FILL){
            $image = clone $thisimage;
            $image->resize($width, $height);
            $this->_resize($thisimage,$width,$height,self::NONE);
            $thisimage->fill('#ffffff');
            $thisimage->fill($image);
            unset($image);
            return $this;
        }
        
        if ($master === NULL)
        {
            // Choose the master dimension automatically
            $master = self::AUTO;
        }
        // Image::WIDTH and self::HEIGHT depricated. You can use it in old projects,
        // but in new you must pass empty value for non-master dimension
        elseif ($master == self::WIDTH AND ! empty($width))
        {
            $master = self::AUTO;
            
            // Set empty height for backvard compatibility
            $height = NULL;
        }
        elseif ($master == self::HEIGHT AND ! empty($height))
        {
            $master = self::AUTO;
            
            // Set empty width for backvard compatibility
            $width = NULL;
        }
        
        if (empty($width))
        {
            if ($master === self::NONE)
            {
                // Use the current width
                $width = $thiswidth;
            }
            else
            {
                // If width not set, master will be height
                $master = self::HEIGHT;
            }
        }
        
        if (empty($height))
        {
            if ($master === self::NONE)
            {
                // Use the current height
                $height = $thisheight;
            }
            else
            {
                // If height not set, master will be width
                $master = self::WIDTH;
            }
        }
        
        switch ($master)
        {
            case self::AUTO:
                // Choose direction with the greatest reduction ratio
                $master = ($thiswidth / $width) > ($thisheight / $height) ? self::WIDTH : self::HEIGHT;
                break;
            case self::INVERSE:
                // Choose direction with the minimum reduction ratio
                $master = ($thiswidth / $width) > ($thisheight / $height) ? self::HEIGHT : self::WIDTH;
                break;
        }
        
        switch ($master)
        {
            case self::WIDTH:
                // Recalculate the height based on the width proportions
                $height = $thisheight * $width / $thiswidth;
                break;
            case self::HEIGHT:
                // Recalculate the width based on the height proportions
                $width = $thiswidth * $height / $thisheight;
                break;
        }
        
        // Convert the width and height to integers
        $width  = round($width);
        $height = round($height);
        
        $thisimage->resize($width, $height);
    }
    public function resize(string $file,string $resize,string $save_file)
    {
        $config=$this->_config->get($resize);
        if (empty($config['height'])&&empty($config['width']))return false;
        extract($config);
        /**
         * @var int $master
         * @var int $width
         * @var int $height
         */
        if (!isset($master))$master=self::AUTO;
        $thisimage=$this->_image_mgr->make($file);
        $this->_resize($thisimage, $width, $height,$master);
        return $thisimage->save($save_file,90);
    }
}