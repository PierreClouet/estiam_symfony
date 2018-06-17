<?php

namespace Estiam\BlogBundle\Manager;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Estiam\BlogBundle\Entity\Commentary;
use Estiam\BlogBundle\Entity\Note;
use Estiam\BlogBundle\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostManager
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;
    private $user;

    /**
     * PostManager constructor.
     * @param ManagerRegistry $doctrine
     * @param TokenStorageInterface $tokenStorage
     * @internal param ManagerRegistry $em
     */

    public function __construct(ManagerRegistry $doctrine, TokenStorageInterface $tokenStorage)
    {
        $this->doctrine = $doctrine;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function getPosts($filter, $direction)
    {
        $postsRepo = $this->doctrine->getRepository(Post::class);
        if ($filter == 'user') {
            $posts = $this->doctrine->getManager()
                ->createQuery('SELECT p, u FROM EstiamBlogBundle:Post p JOIN p.user u WHERE p.state = 1 ORDER BY u.username ' . $direction . '')->getResult();
            dump($posts);
        } else {
            $posts = $postsRepo->findBy(
                array('state' => 1),
                array($filter => $direction)
            );
        }

        return $posts;
    }

    public function getOwnPosts($id_user, $state, $filter, $direction)
    {
        $em = $this->doctrine->getRepository(Post::class);
        $posts = $em->findBy(
            array(
                'user' => $id_user,
                'state' => $state,
            ),
            array(
                $filter => $direction
            )
        );

        return $posts;
    }

    public function getPost($id_post)
    {
        $repository = $this->doctrine->getRepository(Post::class);

        $post = $repository->findOneBy(array('id' => $id_post));

        return $post;
    }

    public function newPost(Post $post)
    {
        $post->setUser($this->user);
        $post->setCreatedAt();
        $em = $this->doctrine->getManager();
        $em->persist($post);
        $em->flush();
    }

    public function updatePost(Post $post)
    {
        $em = $this->doctrine->getManager();
        $em->persist($post);
        $em->flush();
    }

    public function deletePost($id_post)
    {
        $post = $this->getPost($id_post);
        $em = $this->doctrine->getManager();
        foreach ($post->getCommentaries() as $comment) {
            dump($comment->getNote());
            $em->remove($comment);
        }
        $em->remove($post);
        $em->flush();
    }

    public function canPublish(Post $post)
    {
        $state = $post->getState();
        $note = $this->user->getNote();

        if ($state !== 0 && $note < 4.5) {
            return false;
        }

        return true;
    }

    public function newComment(Post $post, Commentary $commentary)
    {
        $commentary->setUser($this->user);
        $commentary->setPost($post);
        $em = $this->doctrine->getManager();
        $em->persist($commentary);
        $em->flush();
    }

    public function newNote($new_note, $note, $userRated, $idAuthor, $comment)
    {
        if (empty($this->hasAlreadyNote($userRated))) {
            $repository = $this->doctrine->getRepository(User::class);
            $user = $repository->findOneBy(array('id' => $userRated));

            $new_note->setNote($note);
            $new_note->setUser($user);
            $new_note->setIdAuthor($idAuthor);
            $new_note->setCommentary($comment);
            $comment->setNote($new_note);
            $em = $this->doctrine->getManager();
            $em->persist($new_note);
            $em->persist($comment);

            $repository = $this->doctrine->getRepository(Note::class);
            $notes = $repository->findBy(array('user' => $user));
            $curUserNote = $user->getNote();
            $allNotes = array($curUserNote);
            foreach ($notes as $note) {
                $allNotes[] = $note->getNote();
            }

            $finalNote = array_sum($allNotes) / count($allNotes);

            $user->setNote($finalNote);
            $em->persist($user);
            $em->flush();
        }
    }

    public function hasAlreadyNote($id_user)
    {
        $repository = $this->doctrine->getRepository(Note::class);
        $note = $repository->findOneBy(array('idAuthor' => $id_user));
        if ($note) {
            return $note->getNote();
        }
        return null;
    }

    public function hasAlreadyNoteComment($id_post_author, $id_comment_author)
    {
        $repository = $this->doctrine->getRepository(Note::class);
        $note = $repository->findOneBy(
            array('idAuthor' => $id_post_author),
            array('user', $id_comment_author)
        );
        if ($note) {
            return $note->getNote();
        }
        return null;
    }
}