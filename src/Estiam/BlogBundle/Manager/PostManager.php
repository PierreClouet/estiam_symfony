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
        if ($filter == 'user') {
            $posts = $this->doctrine->getManager()
                ->createQuery('SELECT p, u FROM EstiamBlogBundle:Post p JOIN p.user u WHERE SIZE(p.commentaries) < 1 AND p.state = 1 ORDER BY u.username ' . $direction . '')->getResult();
        } else {
            $qb = $this->doctrine->getManager()->createQueryBuilder();
            $q = $qb->select(array('p'))
                ->from('EstiamBlogBundle:Post', 'p')
                ->where(
                    $qb->expr()->eq('p.state', 1)
                )
                ->andWhere('SIZE(p.commentaries) < 1')
                ->orderBy('p.' . $filter, $direction)
                ->getQuery();

            return $q->getResult();
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

    public function getCommentedPosts($filter, $direction)
    {
        if ($filter == 'user') {
            $posts = $this->doctrine->getManager()
                ->createQuery('SELECT p, u FROM EstiamBlogBundle:Post p JOIN p.user u JOIN p.commentaries c WHERE ' . $this->user->getId() . ' = c.user AND p.state = 1 ORDER BY u.username ' . $direction . '')->getResult();
        } else {
            $qb = $this->doctrine->getManager()->createQueryBuilder();
            $q = $qb->select(array('p'))
                ->from('EstiamBlogBundle:Post', 'p')
                ->innerJoin('p.commentaries', 'c')
                ->where(':user_id = c.user')
                ->andWhere('p.state = 1')
                ->orderBy('p.' . $filter, $direction)
                ->setParameter('user_id', $this->user->getId())
                ->getQuery();

            return $q->getResult();
        }
        return $posts;
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
            $em->remove($comment);
        }
        foreach ($post->getNotes() as $note) {
            $em->remove($note);
        }
        $em->remove($post);
        $em->flush();

        $this->calcUserAvg($this->user);
    }

    public function canPublish(Post $post)
    {
        $state = $post->getState();
        $note = $this->user->getNote();

        if ($state == 1 && $note < 4.5) {
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

    public function newCommentNote($new_note, $note, $userRated, $idAuthor, $comment)
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $userRated));
        $em = $this->doctrine->getManager();

        $new_note->setNote($note);
        $new_note->setUser($user);
        $new_note->setIdAuthor($idAuthor);
        $new_note->setCommentary($comment);
        $comment->setNote($new_note);
        $em->persist($comment);
        $em->persist($new_note);
        $em->flush();

        $this->calcUserAvg($user);
    }

    public function newPostNote($new_note, $note, $userRated, $idAuthor, $post)
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $userRated));
        $em = $this->doctrine->getManager();

        $new_note->setNote($note);
        $new_note->setUser($user);
        $new_note->setIdAuthor($idAuthor);
        $new_note->setPost($post);
        $em->persist($new_note);
        $em->flush();

        $this->calcUserAvg($user);
    }

    public function calcUserAvg($user)
    {
        $repository = $this->doctrine->getRepository(Note::class);
        $notes = $repository->findBy(array('user' => $user));
        $curUserNote = $user->getNote();
        $allNotes = array($curUserNote);
        foreach ($notes as $el) {
            $allNotes[] = $el->getNote();
        }

        $finalNote = array_sum($allNotes) / count($allNotes);

        $user->setNote($finalNote);
        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();
    }

    public function hasAlreadyNote($id_user, $id_post)
    {
        $repository = $this->doctrine->getRepository(Note::class);
        $note = $repository->findOneBy(
            array(
                'idAuthor' => $id_user,
                'post' => $id_post
            )
        );
        if ($note) {
            return $note->getNote();
        }
        return null;
    }
}