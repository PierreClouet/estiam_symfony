<?php

namespace Estiam\BlogBundle\Controller;

use Estiam\BlogBundle\Entity\Commentary;
use Estiam\BlogBundle\Entity\Note;
use Estiam\BlogBundle\Entity\Post;
use Estiam\BlogBundle\Form\Type\CommentaryType;
use Estiam\BlogBundle\Form\Type\NoteType;
use Estiam\BlogBundle\Form\Type\PostType;
use Estiam\BlogBundle\Manager\PostManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class PostController extends Controller
{
    /**
     * @param Request $request
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list", name="list_post")
     */
    public function listPostAction(Request $request, PostManager $postManager)
    {
        $filter = ($request->request->get('filter_post') ? $request->request->get('filter_post') : 'createdAt');
        $direction = ($request->request->get('direction') ? $request->request->get('direction') : 'desc');
        $posts = $postManager->getPosts($filter, $direction);

        return $this->render('@EstiamBlog/post/list.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * @param Request $request
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list/commented", name="list_post_commented")
     */
    public function listCommentedPostAction(Request $request, PostManager $postManager)
    {
        $filter = ($request->request->get('filter_post') ? $request->request->get('filter_post') : 'createdAt');
        $direction = ($request->request->get('direction') ? $request->request->get('direction') : 'desc');
        $posts = $postManager->getCommentedPosts($filter, $direction);

        return $this->render('@EstiamBlog/post/list.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * @param $id_post
     * @param PostManager $postManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{id_post}", name="view_post")
     */
    public function viewPostAction($id_post, PostManager $postManager, Request $request)
    {

        $post = $postManager->getPost($id_post);

        $comments = $post->getCommentaries();

        $post_note = $postManager->hasAlreadyNote($this->getUser()->getId(), $id_post);

        //create note form
        $new_note = new Note();
        $form_note = $this->createForm(NoteType::class, $new_note);
        $form_note->handleRequest($request);

        if ($form_note->isSubmitted() && $form_note->isValid()) {
            $new_note = $form_note->getData();
            $note = $new_note->getNote();
            $userRated = $new_note->getIdAuthor();
            $idAuthor = $this->getUser()->getId();
            $comment = $new_note->getCommentary();
            $post = $new_note->getPost();
            if ($comment) {
                $postManager->newCommentNote($new_note, $note, $userRated, $idAuthor, $comment);
            } elseif ($post) {
                if (!$postManager->hasAlreadyNote($this->getUser()->getId(), $id_post))
                    $postManager->newPostNote($new_note, $note, $userRated, $idAuthor, $post);
            }

            return $this->redirectToRoute('view_post', array(
                'id_post' => $id_post
            ));
        }

        //create comment form
        $new_comment = new Commentary();
        $form_comment = $this->createForm(CommentaryType::class, $new_comment);
        $form_comment->handleRequest($request);

        if ($form_comment->isSubmitted() && $form_comment->isValid()) {
            $new_comment = $form_comment->getData();

            $postManager->newComment($post, $new_comment);

            return $this->redirectToRoute('view_post', array(
                'id_post' => $id_post
            ));
        }
        return $this->render('@EstiamBlog/post/view.html.twig', array(
            'post' => $post,
            'comments' => $comments,
            'form_comment' => $form_comment->createView(),
            'form_note' => $form_note,
            'post_note' => $post_note
        ));
    }

    /**
     * @param Request $request
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/new", name="new_post")
     */
    public function newPostAction(Request $request, PostManager $postManager)
    {
        $new_post = new Post();
        $form = $this->createForm(PostType::class, $new_post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($postManager->canPublish($new_post)) {
                $new_post = $form->getData();

                $postManager->newPost($new_post);

                return $this->redirectToRoute('list_post');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'Vous ne pouvez pas publier d\'annonces car votre note est inférieure à 4.5');
            }
        }

        return $this->render('@EstiamBlog/post/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param $id_post
     * @param PostManager $postManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/delete/{id_post}", name="post_delete")
     */
    public function deletePostAction($id_post, PostManager $postManager)
    {
        $postManager->deletePost($id_post);

        return $this->redirectToRoute('list_post');
    }
}
