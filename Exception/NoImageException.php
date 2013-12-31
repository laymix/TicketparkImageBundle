<?php

namespace Ticketpark\ImageBundle\Exception;

class NoImageException extends \RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $path The path to the file that is no image
     */
    public function __construct($path)
    {
        parent::__construct(sprintf('The file "%s" is no image', $path));
    }
}
