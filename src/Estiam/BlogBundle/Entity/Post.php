<?php

namespace Estiam\BlogBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="Estiam\BlogBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\OneToMany(targetEntity="Estiam\BlogBundle\Entity\Note", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */

    private $notes;

    /**
     * @return Collection|Note[]
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @ORM\OneToMany(targetEntity="Estiam\BlogBundle\Entity\Commentary", mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */

    private $commentaries;

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentaries()
    {
        return $this->commentaries;
    }

    public function __construct()
    {
        $this->commentaries = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts")
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

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
     * Set createdAt
     * @return Post
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \Datetime('now');

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Post
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

}


