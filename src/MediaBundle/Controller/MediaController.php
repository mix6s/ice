<?php

namespace MediaBundle\Controller;

use MediaBundle\Entity\Album;
use MediaBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * @Route("/", name="media.index")
     * @param Request $request
     * @return Response
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function mediaAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT a FROM MediaBundle:Album a WHERE a.isActive = true ORDER BY a.id DESC');
        $albums = $query->getResult();
        return $this->render('@Media/Media/index.html.twig', [
            'albums' => $albums
        ]);
    }

    /**
     * @Route("/album/{id}", name="media.album")
     * @param Album $album
     * @return Response
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    public function albumAction(Album $album): Response
    {
        $repo = $this->getDoctrine()->getRepository(Image::class);
        $images = $repo->findBy(['album' => $album->getId()]);

        return $this->render('@Media/Media/album.html.twig', [
            'images' => $images,
            'album' => $album
        ]);
    }

}