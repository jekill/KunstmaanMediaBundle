<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Helper\VideoGalleryStrategy;

/**
 * Class that defines a Media object from the AnoBundle in the database
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity
 * @ORM\Table(name="media_gallery_video")
 * @ORM\HasLifecycleCallbacks
 */
class VideoGallery extends Folder{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    public function getStrategy(){
        return new VideoGalleryStrategy();
    }
}