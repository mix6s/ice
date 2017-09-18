<?php
/**
 * Created by PhpStorm.
 * User: ktlle
 * Date: 9/18/17
 * Time: 1:40 AM
 */

namespace ControlBundle\Controller;

use BlogBundle\Entity\Post;
use BlogBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    /**
     * @Route("/posts/list", name="control.posts.list")
     */
    public function blogListAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repo->findAll();
        return $this->render('@Control/blog/list.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/posts/new", name="control.blog.new")
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function newPostAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $post->getImageUrl();
            $fileName = md5(uniqid('', true)).'.'.$file->guessExtension();
            $file->move($this->getParameter('post_images_directory'), $fileName);
            $post->setImageUrl($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('control.posts.list'));
        }
        return $this->render('@Control/blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/posts/delete/{id}", name="control.blog.delete")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function deletePostAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('control.posts.list');
    }

    /**
     * @Route("/posts/show/{id}", name="control.blog.show")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function showPostAction(Post $post)
    {
        return $this->render('@Control/blog/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/posts/edit/{id}", name="control.blog.edit")
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     */
    public function editPostAction(Request $request, Post $post)
    {
        $post->setImageUrl(
            new File($this->getParameter('post_images_directory').'/'.$post->getImageUrl())
        );
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $post->getImageUrl();
            $fileName = md5(uniqid('', true)).'.'.$file->guessExtension();
            $file->move($this->getParameter('post_images_directory'), $fileName);
            $post->setImageUrl($fileName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('control.posts.list'));
        }
        return $this->render('@Control/blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}