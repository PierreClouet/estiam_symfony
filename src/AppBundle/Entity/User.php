<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Estiam\BlogBundle\Entity\Commentary;
use Estiam\BlogBundle\Entity\Note;
use Estiam\BlogBundle\Entity\Post;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    /**
     * @ORM\OneToMany(targetEntity="Estiam\BlogBundle\Entity\Commentary", mappedBy="user")
     */

    private $commentaries;

    /**
     * @return Collection|Commentary[]
     */
    public function getCommentary()
    {
        return $this->commentaries;
    }

    /**
     * @ORM\OneToMany(targetEntity="Estiam\BlogBundle\Entity\Post", mappedBy="user")
     */

    private $posts;

    /**
     * @return Collection|Post[]
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @ORM\OneToMany(targetEntity="Estiam\BlogBundle\Entity\Note", mappedBy="user")
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
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->commentaries = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    /**
     * @var $note
     * @ORM\Column(type="decimal", precision=2, scale=1)
     */
    protected $note = 4.5;

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }
}