<?php

namespace Estiam\BlogBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commentary
 *
 * @ORM\Table(name="commentary")
 * @ORM\Entity(repositoryClass="Estiam\BlogBundle\Repository\CommentaryRepository")
 */
class Commentary
{
    /**
     * @ORM\OneToOne(targetEntity="Estiam\BlogBundle\Entity\Note", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="id_note", referencedColumnName="id", onDelete="CASCADE")
     */

    private $note;

    public function getNote()
    {
        return $this->note;
    }

    public function setNote(Note $note)
    {
        $this->note = $note;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Estiam\BlogBundle\Entity\Post", inversedBy="commentaries")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="commentaries")
     * @ORM\JoinColumn(name="id_author", referencedColumnName="id")
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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;


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
     * Set content
     *
     * @param string $content
     *
     * @return Commentary
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}

