<?php

namespace Estiam\BlogBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="Estiam\BlogBundle\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\OneToOne(targetEntity="Estiam\BlogBundle\Entity\Commentary", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="id_comment", referencedColumnName="id", onDelete="CASCADE")
     */

    private $commentary;

    public function getCommentary()
    {
        return $this->commentary;
    }

    public function setCommentary(Commentary $commentary)
    {
        $this->commentary = $commentary;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notes")
     * @ORM\JoinColumn(name="id_user_rated", referencedColumnName="id")
     */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Estiam\BlogBundle\Entity\Post", inversedBy="notes")
     * @ORM\JoinColumn(name="id_post", referencedColumnName="id", onDelete="CASCADE")
     */
    private $post;

    public function getPost()
    {
        return $this->post;
    }

    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_author", type="integer")
     */
    private $idAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="integer")
     */
    private $note;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $idAuthor
     */
    public function setIdAuthor($idAuthor)
    {
        $this->idAuthor = $idAuthor;
    }

    /**
     * @return int
     */
    public function getIdAuthor()
    {
        return $this->idAuthor;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Note
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}

