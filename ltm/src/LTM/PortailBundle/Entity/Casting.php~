<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 25/06/2014
 * Time: 23:31
 */

namespace LTM\PortailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Casting
{

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CCasting")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CCastingTarget")
     * @Assert\NotBlank()
     */
    private $searched;


    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\User")
     */
    private $members;


    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CDance")
     * @Assert\NotBlank()
     */
    protected $style;

    /**
     * @ORM\OneToOne(targetEntity="LTM\PortailBundle\Entity\User")
     */
    protected $author;

    /**
     * @ORM\Column(type="date")
     */
    protected $creationDate;


    /**
     * @ORM\Column(type="string",length=600,nullable=true)
     * @Assert\NotBlank()
     */
    protected $url;

    /**
     * @ORM\Column(type="text",nullable=true)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public  $file;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;



    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return 'bundles/portail/'.$this->getUploadDir();
    }


    public function upload()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        // utilisez le nom de fichier original ici mais
        // vous devriez « l'assainir » pour au moins éviter
        // quelconques problèmes de sécurité

        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

        // définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->path = $this->file->getClientOriginalName();

        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->file = null;
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/casting';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
        $this->searched = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
        $this->style = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Casting
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Casting
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Casting
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
     * Set path
     *
     * @param string $path
     * @return Casting
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add category
     *
     * @param \LTM\PortailBundle\Entity\CCasting $category
     * @return Casting
     */
    public function addCategory(\LTM\PortailBundle\Entity\CCasting $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \LTM\PortailBundle\Entity\CCasting $category
     */
    public function removeCategory(\LTM\PortailBundle\Entity\CCasting $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add searched
     *
     * @param \LTM\PortailBundle\Entity\CCastingTarget $searched
     * @return Casting
     */
    public function addSearched(\LTM\PortailBundle\Entity\CCastingTarget $searched)
    {
        $this->searched[] = $searched;

        return $this;
    }

    /**
     * Remove searched
     *
     * @param \LTM\PortailBundle\Entity\CCastingTarget $searched
     */
    public function removeSearched(\LTM\PortailBundle\Entity\CCastingTarget $searched)
    {
        $this->searched->removeElement($searched);
    }

    /**
     * Get searched
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSearched()
    {
        return $this->searched;
    }

    /**
     * Add members
     *
     * @param \LTM\PortailBundle\Entity\User $members
     * @return Casting
     */
    public function addMember(\LTM\PortailBundle\Entity\User $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \LTM\PortailBundle\Entity\User $members
     */
    public function removeMember(\LTM\PortailBundle\Entity\User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Add style
     *
     * @param \LTM\PortailBundle\Entity\CDance $style
     * @return Casting
     */
    public function addStyle(\LTM\PortailBundle\Entity\CDance $style)
    {
        $this->style[] = $style;

        return $this;
    }

    /**
     * Remove style
     *
     * @param \LTM\PortailBundle\Entity\CDance $style
     */
    public function removeStyle(\LTM\PortailBundle\Entity\CDance $style)
    {
        $this->style->removeElement($style);
    }

    /**
     * Get style
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set author
     *
     * @param \LTM\PortailBundle\Entity\User $author
     * @return Casting
     */
    public function setAuthor(\LTM\PortailBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \LTM\PortailBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Casting
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
}
