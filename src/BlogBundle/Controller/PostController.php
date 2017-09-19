<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Post controller.
 *
 * @Route("/")
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/", name="blog_index")
     * @Method("GET")
     * @throws \LogicException
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM BlogBundle:Post p ORDER BY p.postedAt DESC')->setMaxResults(3);
        $posts = $query->getResult();
        return $this->render('@Blog/Blog/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/post/{id}", name="blog_post_show")
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        return $this->render('post/show.html.twig', array(
            'post' => $post,
        ));
    }
}
