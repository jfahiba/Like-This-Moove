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
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class Video
{

    use ORMBehaviors\Sluggable\Sluggable ;


    public function getSluggableFields()
    {
        return [ 'author', 'title'  ];
    }

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;


    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CDance")
     * @Assert\NotBlank()
     */
    protected $style;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\User")
     */
    protected $author;

    /**
     * @ORM\Column(type="date")
     */
    protected $creationDate;


    /**
     * @ORM\Column(type="string",length=600,nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="100000000")
     */
    public $file;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $pathImage;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $fileImage;

	/**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vues = 0;

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
        //return '../../../lib/red5/webapps/'.$this->getUploadDir();
    }

    function cleanName($file) {
        $extension = $file->getClientOriginalExtension();
        $name = str_replace($extension, '', $file->getClientOriginalName());
        $string = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
        return uniqid(null, false)    . '-' . preg_replace('/[^A-Za-z0-9\-]/', '', $string) . '.' . $extension ; // Removes special chars.
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

		// définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->path = $this->cleanName($this->file);;
		
        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->file->move($this->getUploadRootDir(), $this->path);

        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->file = null;
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/videos';
        //return 'oflaDemo/streams';
    }

    public function getAbsolutePathImage()
    {
        return null === $this->pathImage ? null : $this->getUploadRootDirImage().'/'.$this->pathImage;
    }

    public function getWebPathImage()
    {
        return null === $this->pathImage ? null : $this->getUploadDirImage().'/'.$this->pathImage;
    }

    public function getUploadRootDirImage()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return 'bundles/portail/'.$this->getUploadDirImage();
    }

    protected function getUploadDirImage()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/videos/images';
    }

    public function uploadImage()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->fileImage) {
            return;
        }

        // utilisez le nom de fichier original ici mais
        // vous devriez « l'assainir » pour au moins éviter
        // quelconques problèmes de sécurité

		// définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->pathImage =  $this->cleanName($this->fileImage);

		
        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->fileImage->move($this->getUploadRootDirImage(), $this->pathImage);

        
        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->fileImage = null;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->style = new \Doctrine\Common\Collections\ArrayCollection();
        $this->creationDate = new \DateTime("now");
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
     * @return Photo
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
     * @return Photo
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
     * @return Photo
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
     * @return Photo
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
     * Add style
     *
     * @param \LTM\PortailBundle\Entity\CDance $style
     * @return Photo
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
     * @return Photo
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
     * @return Video
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
     * Set pathImage
     *
     * @param string $pathImage
     * @return Video
     */
    public function setPathImage($pathImage)
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    /**
     * Get pathImage
     *
     * @return string
     */
    public function getPathImage()
    {
        return $this->pathImage;
    }
	
	public function incrementVues ($em){
        $this->vues++;
        $em->persist($this);
        $em->flush();
    }

    /**
     * Set vues
     *
     * @param integer $vues
     * @return User
     */
    public function setVues($vues)
    {
        $this->vues = $vues;

        return $this;
    }

    /**
     * Get vues
     *
     * @return integer 
     */
    public function getVues()
    {
        return $this->vues;
    }

}
