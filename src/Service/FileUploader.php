<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $imageDirectory;

    public function __construct($imageDirectory)
    {
        $this->imageDirectory = $imageDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getImageDirectory(), $fileName);

        return $fileName;
    }

    public function getImageDirectory()
    {
        return $this->imageDirectory;
    }
}