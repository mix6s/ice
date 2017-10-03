<?php
/**
 * Created by PhpStorm.
 * User: ktlle
 * Date: 9/30/17
 * Time: 10:04 AM
 */

namespace ControlBundle\Controller;

use MediaBundle\DTO\AddImagesDTO;
use MediaBundle\Form\AddImagesDTOType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MediaBundle\Entity\Album;
use MediaBundle\Entity\Image;
use MediaBundle\Form\AlbumType;
use Symfony\Component\HttpFoundation\Response;


class MediaController extends Controller
{

    /**
     * @Route("/media/album/list", name="control.media.album.list")
     * @param Request $request
     * @return Response
     * @throws \LogicException
     */
    public function mediaListAction(Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(Album::class);
        $albums = $repo->findAll();
        return $this->render('@Control/media/list.html.twig', ['albums' => $albums]);
    }

    /**
     * @Route("/media/album/new", name="control.media.album.new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function mediaAlbumNew(Request $request): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            return $this->redirectToRoute('control.media.album.list');
        }
        return $this->render('@Control/media/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/media/album/edit/{id}", name="control.media.album.edit")
     * @param Request $request
     * @param Album $album
     * @return Response
     */
    public function mediaAlbumEdit(Request $request, Album $album): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush();
            return $this->redirectToRoute('control.media.album.list');
        }
        return $this->render('@Control/media/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/media/album/photos/{id}", name="control.media.album.photos")
     * @param Request $request
     * @param Album $album
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     * @throws \UnexpectedValueException
     */
    public function mediaAlbumPhotos(Request $request, Album $album): Response
    {
        $repo = $this->getDoctrine()->getRepository(Image::class);
        $images = $repo->findBy(['album' => $album->getId()]);

        $imagesDto = new AddImagesDTO();
        $form = $this->createForm(AddImagesDTOType::class, $imagesDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($imagesDto->getImages() as $uploadedFile) {
                $fileName = md5(uniqid('', true)).'.'.$uploadedFile->guessExtension();
                $uploadedFile->move($this->getParameter('album_images_directory'), $fileName);
                $image = new Image();
                $image->setAlbum($album);
                $image->setPath($fileName);
                $em->persist($image);
            }
            $em->flush();
            return $this->redirect($request->getRequestUri());
        }
        return $this->render(
            '@Control/media/photos.html.twig',
            [
                'album' => $album,
                'form' => $form->createView(),
                'images' => $images
            ]);
    }

    /**
     * @Route("/media/album/remove/{id}", name="control.media.album.remove")
     * @param Album $album
     * @return Response
     */
    public function mediaAlbumRemove(Album $album): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Image::class);
        $images = $repo->findBy(['album' => $album->getId()]);
        foreach ($images as $image) {
            $em->remove($image);
        }
        $em->remove($album);
        $em->flush();
        return $this->redirectToRoute('control.media.album.list');
    }

    /**
     * @Route("/media/image/remove/{id}", name="control.media.image.remove")
     * @param Request $request
     * @param Image $image
     * @return Response
     */
    public function mediaImageRemove(Request $request, Image $image): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Album $album */
        $album = $image->getAlbum();

        $em->remove($image);
        $em->flush();
        return $this->redirectToRoute('control.media.album.photos', ['id' => $album->getId()]);
    }

    /**
     * @Route("/media/album/choose_thumbnail/{id}", name="control.media.album.choose_thumbnail")
     * @param Album $album
     * @return Response
     */
    public function chooseThumbnail(Album $album): Response
    {
        $repo = $this->getDoctrine()->getRepository(Image::class);
        $images = $repo->findBy(['album' => $album->getId()]);
        return $this->render('@Control/media/choose_thumbnail.html.twig', [
            'album' => $album,
            'images' => $images
        ]);
    }

    /**
     * @Route("/media/album/set_thumbnail/{id}", name="control.media.album.set_thumbnail")
     * @param Image $image
     * @return Response
     */
    public function setThumbnail(Image $image): Response
    {
        /** @var Album $album */
        $album = $image->getAlbum();
        $album->setMainImage($image);
        $em = $this->getDoctrine()->getManager();
        $em->persist($album);
        $em->flush();
        return $this->redirectToRoute('control.media.album.list');
    }

}