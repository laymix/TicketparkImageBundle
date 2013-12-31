<?php

namespace Ticketpark\ImageBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ResizerExtension extends \Twig_Extension
{
    public function __construct(ContainerInterface $container)
    {
        $this->container   = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('resize',      array($this, 'resizeFilter')),
            new \Twig_SimpleFilter('resizeMm',    array($this, 'resizeMmFilter')),
        );
    }

    /**
     * Returns base 64 encoded image, based on mm size and dpi
     *
     * @param string $url
     * @param int $sizeInMm
     * @param int $dpi
     * @return string
     */
    public function resizeFilter($pathOrUrl, $maxSizeInPixel, $filter="crop")
    {
        return $this->container->get('ticketpark.image.resizer')->resize($pathOrUrl, $filter, $maxSizeInPixel);
    }

    /**
     * Resize an image, based on a target size in mm and a dpi resolution
     *
     * @param string $url
     * @param int $sizeInMm
     * @param int $dpi
     * @return string
     */
    public function resizeMmFilter($pathOrUrl, $maxSizeInMm, $dpi=150, $filter='crop')
    {
        return $this->container->get('ticketpark.image.resizer')->resizeMm($pathOrUrl, $filter, $maxSizeInMm, $dpi);
    }

    public function getName()
    {
        return 'ticketpark_image_resizer_extension';
    }
}