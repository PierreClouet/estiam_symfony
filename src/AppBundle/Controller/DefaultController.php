<?php

namespace AppBundle\Controller;

use Estiam\BlogBundle\Manager\PostManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, PostManager $postManager)
    {
        $filter = ($request->request->get('filter_post') ? $request->request->get('filter_post') : 'createdAt');
        $direction = ($request->request->get('direction') ? $request->request->get('direction') : 'desc');
        $posts = $postManager->getPosts($filter, $direction);

        return $this->render('@EstiamBlog/post/list.html.twig', array(
            'posts' => $posts
        ));
    }
}
