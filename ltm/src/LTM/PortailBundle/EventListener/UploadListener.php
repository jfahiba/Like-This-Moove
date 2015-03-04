<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 31/07/2014
 * Time: 22:13
 */

namespace LTM\PortailBundle\EventListener;

use LTM\PortailBundle\Entity\Image;
use LTM\PortailBundle\Entity\Video;
use LTM\PortailBundle\Entity\Advertisement;
use LTM\PortailBundle\Entity\VideoTuto;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\Security\Core\SecurityContext;



class UploadListener
{
    public function __construct(SecurityContext $security, $doctrine, $actionManager)
    {
        $this->security = $security;
        $this->doctrine = $doctrine;
        $this->actionManager = $actionManager;
    }


    public function onUpload(PostPersistEvent $event)
    {

        /*
         $file = $event->getFile();
        $response = $event->getResponse();
        $response['files'] = [
            'name' => $file->getFilename(),
            'size' => $file->getSize(),
            'url' => $url,
            'deleteUrl' => $deleteUrl,
            'deleteType' => 'DELETE'
        ];*/



        if ($event->getType() == "videos") {
            $this->persistVideo($event);
        } elseif ($event->getType() == "tutos") {
            $this->persistTutos($event);
        } elseif ($event->getType() == "images") {
            $this->persistImage($event);
        } elseif ($event->getType() == "annonces") {
            $this->persistAdvertisement($event);
        }
		

    }

    private function persistVideo(PostPersistEvent $event){

        $em = $this->doctrine->getManager();
        $usr= $this->security->getToken()->getUser();

        $video = new Video();
        $video->setCreationDate(new \DateTime("now"));
        $video->setAuthor($usr);

        $path = $event->getFile()->getBasename();
        $video->setPath($path);


        $video->setTitle($event->getFile()->getBaseName());
        $video->setDescription($event->getFile()->getBaseName());

        $em->persist($video);
        $em->flush();

        $actionManager = $this->actionManager;
        $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
        $em->persist($subject);
        $action        = $actionManager->create($subject,  'ajouter une video',
            array('directComplement' => $video->getId(),
                'payante' => false,
                'slug' => $video->getSlug()) );

        $actionManager->updateAction($action);

    }

    private function persistTutos(PostPersistEvent $event){

        $em = $this->doctrine->getManager();
        $usr= $this->security->getToken()->getUser();

        $video = new VideoTuto();
        $video->setCreationDate(new \DateTime("now"));
        $video->setAuthor($usr);

        $path = $event->getFile()->getBasename();
        $video->setPath($path);
        $video->setPrice(0);


        $video->setTitle($event->getFile()->getBaseName());
        $video->setDescription($event->getFile()->getBaseName());

        $em->persist($video);
        $em->flush();

        $actionManager = $this->actionManager;
        $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
        $em->persist($subject);
        $action        = $actionManager->create($subject,  'ajouter une video',
            array('directComplement' => $video->getId(),
                'payante' => true,
                'slug' => $video->getSlug()) );

        $actionManager->updateAction($action);

    }


    private function persistImage(PostPersistEvent $event){

        $em = $this->doctrine->getManager();
        $usr= $this->security->getToken()->getUser();

        $image = new Image();
        $image->setCreationDate(new \DateTime("now"));
        $image->setAuthor($usr);

        $path = $event->getFile()->getBasename();
        $image->setPath($path);


        $image->setTitle($event->getFile()->getBaseName());
        $image->setDescription($event->getFile()->getBaseName());

        $em->persist($image);
        $em->flush();

        $actionManager = $this->actionManager;
        $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
        $em->persist($subject);
        $action        = $actionManager->create($subject,  'ajouter une image',
            array('directComplement' => $image->getAbsolutePath() ,
                'slug' => $image->getSlug() ) );
        $actionManager->updateAction($action);
    }
	
	    private function persistAdvertisement(PostPersistEvent $event){

        $em = $this->doctrine->getManager();
        $usr= $this->security->getToken()->getUser();

        $advertisement = new Advertisement();
            $advertisement->setCreationDate(new \DateTime("now"));
        $advertisement->setAuthor($usr);

        $path = $event->getFile()->getBasename();
        $advertisement->setPath($path);


        $advertisement->setTitle($event->getFile()->getBaseName());
        $advertisement->setDescription($event->getFile()->getBaseName());

        $em->persist($advertisement);
        $em->flush();

        $actionManager = $this->actionManager;
        $subject       = $actionManager->findOrCreateComponent('\User', $usr->getSlug());
        $em->persist($subject);
        $action        = $actionManager->create($subject,  'ajouter une annonce',
            array('directComplement' => $advertisement->getAbsolutePath(),
                'slug' => $advertisement->getSlug()) );
        $actionManager->updateAction($action);

    }
}