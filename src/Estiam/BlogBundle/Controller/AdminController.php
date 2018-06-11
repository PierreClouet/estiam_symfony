<?php


namespace Estiam\BlogBundle\Controller;


use Estiam\BlogBundle\Entity\Post;
use Estiam\BlogBundle\Form\Type\PostType;
use Estiam\BlogBundle\Manager\PostManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller
 * @Route("/admin");
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@EstiamBlog/admin/index.html.twig');
    }

    /**
     * @route("/list", name="admin_post_list")
     * @param Request $request
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, PostManager $postManager)
    {
        $state = ($request->request->get('state') ? $request->request->get('state') : 0);
        $posts = $postManager->getOwnPosts($this->getUser()->getId(), $state);
        return $this->render('@EstiamBlog/admin/list.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * @param $id_post
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{id_post}", name="admin_post_view")
     */
    public function viewAction($id_post, PostManager $postManager)
    {

        $post = $postManager->getPost($id_post);

        return $this->render('@EstiamBlog/admin/view.html.twig', array(
            'post' => $post
        ));
    }

    /**
     * @param Request $request
     * @param $id_post
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/update/{id_post}", name="admin_post_update")
     */
    public function updatePostAction(Request $request, $id_post, PostManager $postManager)
    {
        $post = $postManager->getPost($id_post);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            $postManager->updatePost($post);

            return $this->redirectToRoute('admin_post_view', array(
                'id_post' => $id_post
            ));
        }

        return $this->render('@EstiamBlog/post/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $id_post
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/delete/{id_post}", name="admin_post_delete")
     */
    public function deletePostAction($id_post, PostManager $postManager)
    {
        $postManager->deletePost($id_post);
        return $this->redirectToRoute('admin_post_list');
    }
}