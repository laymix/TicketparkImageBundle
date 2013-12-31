<?php

namespace Ticketpark\ImageBundle\Resizer;

interface ResizerInterface
{
    /**
     * Resize an image
     *
     * @param  string   $pathToFileOrUrl
     * @param  string   $style              "crop" or "resize"
     * @param  int      $size               max image size in pixels
     * @return string   Path to resized file
     */
    public function resize($pathToFileOrUrl, $style, $size);
}