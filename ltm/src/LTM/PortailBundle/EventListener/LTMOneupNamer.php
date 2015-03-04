<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 31/07/2014
 * Time: 23:17
 */

namespace LTM\PortailBundle\EventListener;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\File;


class LTMOneupNamer implements NamerInterface
{
    public function name(FileInterface $file)
    {

        return $this->nameLTM($file);
    }

    public function nameLTM(File\UploadedFile $file)
    {
 ;
        return $this->cleanName($file);

    }

    function cleanName($file) {
        $extension = $file->getClientOriginalExtension();
        $name = str_replace($extension, '', $file->getClientOriginalName());
        $string = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
        return uniqid(null, false) .  '-' . preg_replace('/[^A-Za-z0-9\-]/', '', $string) . '.' . $extension ; // Removes special chars.
    }

}