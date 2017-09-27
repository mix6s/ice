<?php
/**
 * Created by PhpStorm.
 * User: ktlle
 * Date: 9/27/17
 * Time: 2:15 AM
 */

namespace MediaBundle\DTO;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class AddImagesDTO
{
    public $images;

    /**
     * @return null|UploadedFile[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

}