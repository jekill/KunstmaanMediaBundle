<?php

namespace Kunstmaan\MediaBundle\Helper;

/**
 * Comment controller.
 */
class ImageGalleryStrategy implements GalleryStrategyInterface{

    public function getName()
    {
        return 'ImageGallery';
    }

    public function getType()
    {
        return 'image';
    }

    public function getNewGallery()
    {
        return new \Kunstmaan\MediaBundle\Entity\ImageGallery();
    }

    public function getGalleryClassName()
    {
        return 'Kunstmaan\MediaBundle\Entity\ImageGallery';
    }
}

?>