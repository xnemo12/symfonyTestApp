<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 03.02.2019
 * Time: 14:43
 */

namespace App\Util;

use App\Entity\Attachment;
use Symfony\Component\HttpFoundation\File\File;

class FileUpload
{
    private $baseDir;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @param File $file
     * @param string $subdir
     * @return string
     */
    public function upload(File $file, string $subdir)
    {
        $destPath = $this->baseDir . '/public/uploads/' . $subdir . '/';
        $fileName = $this->createFilename($file);
        $file->move($destPath, $fileName);
        return $fileName;
    }

    /**
     * @param File $file
     * @return string
     */
    private function createFilename(File $file)
    {
        $fileName = sha1($file->getFilename(). $file->getSize() . microtime().mt_rand());
        return sprintf('%s.%s', $fileName, $file->guessExtension());
    }

    public function remove(Attachment $attachment)
    {
        $filePath = $this->baseDir . '/public/uploads/' . $attachment->getPath();
        unlink($filePath);
    }

    public function getFilePath($path)
    {
        return $this->baseDir . '/public/uploads/' . $path;
    }
}