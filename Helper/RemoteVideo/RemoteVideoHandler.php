<?php

namespace Kunstmaan\MediaBundle\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Form\RemoteVideo\RemoteVideoType;

use Kunstmaan\MediaBundle\Helper\Media\AbstractMediaHandler;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\MediaBundle\Helper\StrategyInterface;

use Kunstmaan\MediaBundle\Entity\Folder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Form\VideoType;
use Kunstmaan\MediaBundle\AdminList\VideoListConfigurator;
use Kunstmaan\MediaBundle\Entity\Video;

/**
 * RemoteVideoStrategy
 */
class RemoteVideoHandler extends AbstractMediaHandler
{

    /**
     * @var string
     */
    const CONTENT_TYPE = "remote/video";

    /**
     * @var string
     */
    const TYPE = 'video';

    /**
     * @return string
     */
    public function getName()
    {
        return "Remote Video Handler";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return RemoteVideoHandler::TYPE;
    }

    /**
     * @return \Kunstmaan\MediaBundle\Form\VideoType
     */
    public function getFormType()
    {
        return new RemoteVideoType();
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function canHandle(Media $media)
    {
        if ($media->getContentType() == RemoteVideoHandler::CONTENT_TYPE) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return Video
     */
    public function getFormHelper(Media $media)
    {
        return new RemoteVideoHelper($media);
    }

    /**
     * @param Media $media
     *
     * @throws \RuntimeException when the file does not exist
     */
    public function prepareMedia(Media $media)
    {
        if (null == $media->getUuid()) {
            $uuid = uniqid();
            $media->setUuid($uuid);
        }
        $video = new RemoteVideoHelper($media);
        $code = $video->getCode();
        //update thumbnail
        switch($video->getType()) {
            case 'youtube':
                $video->setThumbnailUrl("http://img.youtube.com/vi/" . $code . "/0.jpg");
                break;
            case 'vimeo':
                $xml = simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
                $video->setThumbnailUrl((string) $xml->video->thumbnail_large);
                break;
            case 'dailymotion':
                $json = json_decode(file_get_contents("https://api.dailymotion.com/video/".$code."?fields=thumbnail_large_url"));
                $video->setThumbnailUrl($json->thumbnail_large_url);
                break;
        }
    }

    /**
     * @param Media $media
     */
    public function saveMedia(Media $media)
    {
    }

    /**
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function updateMedia(Media $media)
    {


    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
                'video' => array(
                        'path'   => 'KunstmaanMediaBundle_folder_videocreate',
                        'params' => array(
                                'folderId' => $params['folderId']
                        )
                )
        );
    }

    /**
     * @param mixed $data
     *
     * @return Media
     */
    public function createNew($data)
    {
        $result = null;
        if (is_string($data)) {
            if (strpos($data, 'http') !== 0) {
                $data = "http://" . $data;
            }
            $parsedUrl = parse_url($data);
            switch($parsedUrl['host']) {
                case 'www.youtube.com':
                case 'youtube.com':
                    parse_str($parsedUrl['query'], $queryFields);
                    $code = $queryFields['v'];
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('youtube');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Youtube ' . $code);
                    break;
                case 'www.vimeo.com':
                case 'vimeo.com':
                    $code = substr($parsedUrl['path'], 1);
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('vimeo');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Vimeo ' . $code);
                    break;
                case 'www.dailymotion.com':
                case 'dailymotion.com':
                    $code = substr($parsedUrl['path'], 7);
                    $result = new Media();
                    $video = new RemoteVideoHelper($result);
                    $video->setType('dailymotion');
                    $video->setCode($code);
                    $result = $video->getMedia();
                    $result->setName('Dailymotion ' . $code);
                    break;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getShowTemplate(Media $media)
    {
        return 'KunstmaanMediaBundle:Media\RemoteVideo:show.html.twig';
    }

    /**
     * @param Media  $media    The media entity
     * @param string $basepath The base path
     * @param int    $width    The prefered width of the thumbnail
     * @param int    $height   The prefered height of the thumbnail
     *
     * @return string
     */
    public function getThumbnailUrl(Media $media, $basepath, $width = -1, $height = -1)
    {
        $helper = new RemoteVideoHelper($media);

        return $helper->getThumbnailUrl();
    }

    /**
     * @return multitype:string
     */
    public function getAddFolderActions()
    {
        return array(
                RemoteVideoHandler::TYPE => array(
                    'type' => RemoteVideoHandler::TYPE,
                    'name' => 'media.video.add')
                );
    }

}