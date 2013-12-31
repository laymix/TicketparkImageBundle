<?php

namespace Ticketpark\ImageBundle\Resizer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Ticketpark\FileBundle\FileHandler\FileHandlerInterface;
use Ticketpark\ImageBundle\Exception\NoImageException;

class Resizer implements ResizerInterface
{
    public function __construct(FileHandlerInterface $fileHandler)
    {
        $this->fileHandler       = $fileHandler;
    }

    /**
     * @inheritDoc
     */
    public function resize($pathToFileOrUrl, $style, $maxSizeInPixel)
    {
        $pathToFileOrUrl = $this->fileHandler->get($pathToFileOrUrl);

        return $this->handleResize($pathToFileOrUrl, $style, $maxSizeInPixel);
    }

    /**
     * Resize an image, dependent on target size in mm and resolution in dpi
     *
     * This is mainly used to create printable products (like pdfs)
     *
     * @param string $pathOrUrl
     * @param string $style
     * @param int    $maxSizeInMm
     * @param int    $dpi
     * @return string Path to resized file
     */
    public function resizeMm($pathOrUrl, $style, $maxSizeInMm, $dpi)
    {
        $maxSizeInPixel = round($maxSizeInMm * 0.0393700787 * $dpi);

        return $this->resize($pathOrUrl, $style, $maxSizeInPixel);
    }

    /**
     * Prepare and handle the resizing of the image
     *
     * @param string $pathToFile
     * @param string $style
     * @param int    $maxSizeInPixel
     * @return string   Path to resized file
     */
    protected function handleResize($pathToFile, $style, $maxSizeInPixel)
    {
        if (!$file = $this->fileHandler->fromCache($pathToFile, array($style, $maxSizeInPixel))) {

            if (!$this->isImage($pathToFile)) {
                throw new NoImageException($pathToFile);
            }

           $pathinfo = pathinfo($pathToFile);
           if (!isset($pathinfo['extension']) || '' == $pathinfo['extension'] || null == $pathinfo['extension']) {
               $fileHandle = new File($pathToFile);
               $format = $fileHandle->guessExtension();
           } else {
               $format = $pathinfo['extension'];
           }

            // Resize and cache
            $file = $this->fileHandler->cache('', $pathToFile, array($style, $maxSizeInPixel));
            $this->actualResize($pathToFile, $file, $style, $maxSizeInPixel, $format);
       }

        return $file;
    }

    /**
     * The actual resizing of the image
     *
     * @param string $pathToFile
     * @param string $style
     * @param int    $maxSizeInPixel
     * @return string
     */
    protected function actualResize($pathToFile, $destinationFile, $style, $maxSizeInPixel, $format=null)
    {
        $options = array();
        if (null !== $format) {
            $options['format'] = $format;
        }

        $imagine = new \Imagine\Gd\Imagine();
        $size = new \Imagine\Image\Box($maxSizeInPixel, $maxSizeInPixel);

        if ($style == 'crop') {
            $imagine->open(realpath($pathToFile))->thumbnail($size, 'outbound')->save($destinationFile, $options);
        } else {
            $imagine->open(realpath($pathToFile))->thumbnail($size, 'inset')->save($destinationFile, $options);
        }
    }

    /**
     * Checks if a file is an image
     *
     * @param $file
     * @return bool
     */
    protected function isImage($file)
    {
       if (!$a = @getimagesize($file)) {

           return false;
       };

       if (!is_array($a) || !isset($a[2])) {

           return false;
       }

       if(in_array($a[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {

           return true;
       }

       return false;
    }
}