<?php

namespace Kunstmaan\MediaBundle\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityNotFoundException;

class MediaRepository extends EntityRepository
{
    public function save(Media $media)
    {
        $em = $this->getEntityManager();
        $em->persist($media);
        $em->flush();
    }

    public function delete(Media $media)
    {
        $em = $this->getEntityManager();
        $em->remove($media);
        $em->flush();
    }

    public function getMedia($media_id)
    {
        $media = $this->find($media_id);
        if (!$media) {
            throw new EntityNotFoundException('The id given for the media is not valid.');
        }
        return $media;
    }

    /**
     * @todo Is this actually in use somewhere?
     *
     * @param unknown_type $picture_id
     * @param EntityManager $em
     * @throws EntityNotFoundException
     * @return unknown
     */
    public function getPicture($picture_id, EntityManager $em){
        $picture = $em->getRepository('KunstmaanMediaBundle:Image')->find($picture_id);
        if (!$picture){
            throw new EntityNotFoundException('Unable to find image.');
        }

        return $picture;
    }
}