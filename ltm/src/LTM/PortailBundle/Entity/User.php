<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 25/06/2014
 * Time: 23:31
 */

namespace LTM\PortailBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * @ORM\Entity(repositoryClass="LTM\PortailBundle\Entity\UserRepository")
 * @ORM\Table(name="User")
 * @UniqueEntity(fields="username", message="Le pseudo est déjà utilisé")
 *
 */
class User extends ContainerAware implements UserInterface, \Serializable, ParticipantInterface

{
    use ORMBehaviors\Sluggable\Sluggable ;


    public function getSluggableFields()
    {
        return [ 'country', 'city', 'username'  ];
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")0
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 4096)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="text",length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CDance")
     */
    private $style;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\VideoTuto", cascade={"all"})
     */
    private $boughtVideos;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Credit", cascade={"all"})
     */
    private $credit;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\Abonnement", cascade={"all"})
     */
    private $abonnement;

    /**
     * @ORM\ManyToOne(targetEntity="LTM\PortailBundle\Entity\CCompte")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="LTM\PortailBundle\Entity\CStatus")
     */
    private $status;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $mooverDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $webSite;


    /**
     * @Assert\NotBlank()
     * @Assert\True()
     */
    protected $termsAccepted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $publishProfil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $receiveMailDigests;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $keepHistoric;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showPersonalInformation = true;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $historicDays;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vues = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $phone;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $agentPhone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $top500;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $googleplus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $skype;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $aPropos;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
    private $actualite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $biographie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $awards;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contrats;


    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $rolesStr;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $username;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;


    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $pathCover;


    /**
     * @Assert\File(maxSize="6000000")
     */
    public $fileCover;


    private $options;

    public function getName()
    {
        return $this->firstName.' '.$this->lastName;
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set plainPassword
     *
     * @param string $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {

        $this->plainPassword =  password_hash($plainPassword, PASSWORD_BCRYPT, array('cost' => 12));

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->style = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->lastLoginDate = new \DateTime("now");
        $this->boughtVideos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->credit = new \LTM\PortailBundle\Entity\Credit();

        $this->abonnement = new \LTM\PortailBundle\Entity\Abonnement();

        //$this->addStatus($this->findStatusId('Nouveau'));
    }


    private function findStatusId ($name){

        $status = $this->container->get('doctrine')
            ->getRepository('PortailBundle:CStatus')
            ->findOneBy(array('nom' => $name));

        if (!$status) {
            throw $this->createNotFoundException(
                'No status found for name '.$name
            );
        }
        return $status;

    }

    /**
     * Add style
     *
     * @param CDance $style
     * @return User
     */
    public function addStyle(CDance $style)
    {
        $this->style[] = $style;

        return $this;
    }

    /**
     * Remove style
     *
     * @param CDance $style
     */
    public function removeStyle(CDance $style)
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
     * Set style
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }
	
    /**
     * Set category
     *
     * @param CCompte $category
     * @return User
     */
    public function setCategory(CCompte $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return CCompte
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }

    /**
     * Set rolesStr
     *
     * @param string $rolesStr
     * @return User
     */
    public function setRolesStr($rolesStr)
    {
        $this->rolesStr = $rolesStr;

        return $this;
    }

    /**
     * Get rolesStr
     *
     * @return string
     */
    public function getRolesStr()
    {
        return $this->rolesStr;
    }


    public function getAbsolutePath()
    {

        return (null == $this->path or "" == $this->path )?
            ( ($this->gender == 0 or $this->gender == 4) ? 'bundles/portail/uploads/profiles/avatarh.jpg' :
            'bundles/portail/uploads/profiles/avatarf.jpg' )
            : $this->getUploadRootDir().'/'.$this->path;
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

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/profiles';
    }

    public function getAbsolutePathCover()
    {
        return null === $this->pathCover ? null : $this->getUploadRootDirCover().'/'.$this->pathCover;
    }

    public function getWebPathCover()
    {
        return null === $this->pathCover ? null : $this->getUploadDirCover().'/'.$this->pathCover;
    }

    public function getUploadRootDirCover()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return 'bundles/portail/'.$this->getUploadDirCover();
    }

    protected function getUploadDirCover()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/profiles/covers';
    }

    function cleanName($file) {
        $extension = $file->getClientOriginalExtension();
        $name = str_replace($extension, '', $file->getClientOriginalName());
        $string = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
        return uniqid(null, false)  . '-' . preg_replace('/[^A-Za-z0-9\-]/', '', $string) . '.' . $extension ; // Removes special chars.
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
        $this->path = $this->cleanName($this->file);

		
        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé

        $this->file->move($this->getUploadRootDir(), $this->path);


        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->file = null;
    }

    public function uploadCover()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->fileCover) {
            return;
        }

        // utilisez le nom de fichier original ici mais
        // vous devriez « l'assainir » pour au moins éviter
        // quelconques problèmes de sécurité

		// définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->pathCover = $this->cleanName($this->fileCover);
		
        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->fileCover->move($this->getUploadRootDirCover(), $this->pathCover); 
        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->fileCover = null;
    }



    public function hasBought($video)
    {

        return $this->boughtVideos->contains($video);
    }

    public function buy(VideoTuto $video, $em)
    {

        if ($this->getCredit()->canBuy($video->getPrice() )){
            $this->getCredit()->decrement($video->getPrice() ,
                $this->username . " a acheté une vidéo de " . $video->getAuthor()->getUsername() . " : " . $video->getTitle() );

            $this->addBoughtVideo($video);
            $this->upload();
            $em->persist($this);
            $em->flush();
            return true;
        } else {
            return false;
        }

    }
	
	public function buyAnnonce($advertisement, $price, $em)
    {
		
        if ($this->getCredit()->canBuy($price )){
            $this->getCredit()->decrement($price , $this->username . " a payé " . $price/5 ." jours de publication d'une annonce Pro pour " . $price . " credits"  );

            $em->persist($this);
            $em->flush();
            return true;
        } else {
            return false;
        }

    }

    public function buyCredits($amount, $em)
    {

        $this->getCredit()->increment($amount ,
            $this->username . " a acheté " . $amount ." crédits "  );


        $em->persist($this);
        $em->flush();
        return true;
    }

    public function offerCredits($amount, $giver, $em)
    {

        $this->getCredit()->increment($amount ,
            $this->username . " a recu " . $amount ." crédits de " .$giver   );

        $em->persist($this);
        $em->flush();
        return true;
    }

    public function updateAbonnement($aboname, $moovertype, $accounttype, $cancelHash, $aboprice, $abocurrency , $abofrequency, $abostartDate, $aboendDate, $aboStatus, $aboToken,  $aboDesc,  $aboPeriod, $em)
    {

        if ($this->abonnement == null) {
            $this->abonnement = new \LTM\PortailBundle\Entity\Abonnement();
        }
		
		$startDate = new \DateTime($abostartDate);
        $endDate = new \DateTime($aboendDate); // $startDate->add(new \DateInterval("P12M")) ;
		
		$historic = new CreditHistoric();
        $historic->setAmount(0);
		if ($aboname == 'Pro (Essai gratuit 14 jours)') {
        $historic->setMovement($this->username .' a annule son ' .$this->abonnement->getAboDesc().
            ' Date de resiliation: ' . (new \DateTime("now"))->format('Y-m-d H:i:s'));
		} else {
			$historic->setMovement($this->username .' a renouvellé son ' .$aboDesc.
            'Début de l\'abonnement: le ' . $startDate->format('Y-m-d H:i:s')
            . '. Fin de l\'abonnement : ' . $endDate->format('Y-m-d H:i:s')  );
		}
        $this->getCredit()->addCredithistoric($historic);
		
        $this->abonnement->setAboToken($aboToken);
        $this->abonnement->setAboCancelHash($cancelHash);
        $this->abonnement->setAboCurrency($abocurrency);
        $this->abonnement->setAboDesc($aboDesc);
        $this->abonnement->setAboFrequency($abofrequency);
        $this->abonnement->setAboPeriod($aboPeriod);
        $this->abonnement->setAboPrice($aboprice);

        $this->abonnement->setAboStartDate($startDate);
        $this->abonnement->setAboEndDate($endDate);
        $this->abonnement->setAboStatus($aboStatus);
        $this->abonnement->setMoovertype($moovertype);
        $this->abonnement->setAccounttype($accounttype);



        $category = $em
            ->getRepository('PortailBundle:CPublish')
            ->findOneBy(array('nom' => $aboname));

        $this->category = $category;


        

        $em->persist($this);
        $em->flush();
        return true;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return User
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
        return
            (null == $this->path or "" == $this->path )?
                ( ($this->gender == 0 or $this->gender == 4) ? 'avatarh.jpg' :
                    'avatarf.jpg' )
                :  $this->path;
    }


    /**
     * Set mooverDate
     *
     * @param \DateTime $mooverDate
     * @return User
     */
    public function setMooverDate($mooverDate)
    {
        $this->mooverDate = $mooverDate;

        return $this;
    }

    /**
     * Get mooverDate
     *
     * @return \DateTime
     */
    public function getMooverDate()
    {
        return $this->mooverDate;
    }

    /**
     * Set lastLoginDate
     *
     * @param \DateTime $lastLoginDate
     * @return User
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    /**
     * Get lastLoginDate
     *
     * @return \DateTime
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * Set webSite
     *
     * @param string $webSite
     * @return User
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * Get webSite
     *
     * @return string
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return User
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return User
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set aPropos
     *
     * @param string $aPropos
     * @return User
     */
    public function setAPropos($aPropos)
    {
        $this->aPropos = $aPropos;

        return $this;
    }

    /**
     * Get aPropos
     *
     * @return string
     */
    public function getAPropos()
    {
        return $this->aPropos;
    }
	
	/**
     * Set actualite
     *
     * @param string $actualite
     * @return User
     */
    public function setActualite($actualite)
    {
        $this->actualite = $actualite;

        return $this;
    }

    /**
     * Get actualite
     *
     * @return string
     */
    public function getActualite()
    {
        return $this->actualite;
    }
	
	

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }



    /**
     * Get status
     *
     * @return \LTM\PortailBundle\Entity\CStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add status
     *
     * @param \LTM\PortailBundle\Entity\CStatus $status
     * @return User
     */
    public function addStatus(\LTM\PortailBundle\Entity\CStatus $status)
    {
        $this->status[] = $status;

        return $this;
    }

    /**
     * Remove status
     *
     * @param \LTM\PortailBundle\Entity\CStatus $status
     */
    public function removeStatus(\LTM\PortailBundle\Entity\CStatus $status)
    {
        $this->status->removeElement($status);
    }

    /**
     * Set publishProfil
     *
     * @param boolean $publishProfil
     * @return User
     */
    public function setPublishProfil($publishProfil)
    {
        $this->publishProfil = $publishProfil;

        return $this;
    }

    /**
     * Get publishProfil
     *
     * @return boolean
     */
    public function getPublishProfil()
    {
        return $this->publishProfil;
    }

    /**
     * Set keepHistoric
     *
     * @param boolean $keepHistoric
     * @return User
     */
    public function setKeepHistoric($keepHistoric)
    {
        $this->keepHistoric = $keepHistoric;

        return $this;
    }

    /**
     * Get keepHistoric
     *
     * @return boolean
     */
    public function getKeepHistoric()
    {
        return $this->keepHistoric;
    }

    /**
     * Set historicDays
     *
     * @param integer $historicDays
     * @return User
     */
    public function setHistoricDays($historicDays)
    {
        $this->historicDays = $historicDays;

        return $this;
    }

    /**
     * Get historicDays
     *
     * @return integer
     */
    public function getHistoricDays()
    {
        return $this->historicDays;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set googleplus
     *
     * @param string $googleplus
     * @return User
     */
    public function setGoogleplus($googleplus)
    {
        $this->googleplus = $googleplus;

        return $this;
    }

    /**
     * Get googleplus
     *
     * @return string
     */
    public function getGoogleplus()
    {
        return $this->googleplus;
    }





    /**
     * Set skype
     *
     * @param string $skype
     * @return User
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }


    /**
     * Set receiveMailDigests
     *
     * @param boolean $receiveMailDigests
     * @return User
     */
    public function setReceiveMailDigests($receiveMailDigests)
    {
        $this->receiveMailDigests = $receiveMailDigests;

        return $this;
    }

    /**
     * Get receiveMailDigests
     *
     * @return boolean
     */
    public function getReceiveMailDigests()
    {
        return $this->receiveMailDigests;
    }

    /**
     * Set showPersonalInformation
     *
     * @param boolean $showPersonalInformation
     * @return User
     */
    public function setShowPersonalInformation($showPersonalInformation)
    {
        $this->showPersonalInformation = $showPersonalInformation;

        return $this;
    }

    /**
     * Get showPersonalInformation
     *
     * @return boolean
     */
    public function getShowPersonalInformation()
    {
        return $this->showPersonalInformation;
    }

    /**
     * Set height
     *
     * @param string $height
     * @return User
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set top500
     *
     * @param string $top500
     * @return User
     */
    public function setTop500($top500)
    {
        $this->top500 = $top500;

        return $this;
    }

    /**
     * Get top500
     *
     * @return string
     */
    public function getTop500()
    {
        return $this->top500;
    }

    /**
     * Add boughtVideos
     *
     * @param \LTM\PortailBundle\Entity\VideoTuto $boughtVideos
     * @return User
     */
    public function addBoughtVideo(\LTM\PortailBundle\Entity\VideoTuto $boughtVideo)
    {
        $this->boughtVideos[] = $boughtVideo;

        return $this;
    }

    /**
     * Remove boughtVideos
     *
     * @param \LTM\PortailBundle\Entity\VideoTuto $boughtVideos
     */
    public function removeBoughtVideo(\LTM\PortailBundle\Entity\VideoTuto $boughtVideo)
    {
        $this->boughtVideos->removeElement($boughtVideo);
    }

    /**
     * Get boughtVideos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoughtVideos()
    {
        return $this->boughtVideos;
    }

    /**
     * Set credit
     *
     * @param \LTM\PortailBundle\Entity\Credit $credit
     * @return User
     */
    public function setCredit(\LTM\PortailBundle\Entity\Credit $credit = null)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return \LTM\PortailBundle\Entity\Credit
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set abonnement
     *
     * @param \LTM\PortailBundle\Entity\Abonnement $abonnement
     * @return User
     */
    public function setAbonnement(\LTM\PortailBundle\Entity\Abonnement $abonnement = null)
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    /**
     * Get abonnement
     *
     * @return \LTM\PortailBundle\Entity\Abonnement
     */
    public function getAbonnement()
    {
        return $this->abonnement;
    }

    /**
     * Set pathCover
     *
     * @param string $pathCover
     * @return User
     */
    public function setPathCover($pathCover)
    {
        $this->pathCover = $pathCover;

        return $this;
    }

    /**
     * Get pathCover
     *
     * @return string 
     */
    public function getPathCover()
    {
        return $this->pathCover;
    }

    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (Boolean) $termsAccepted;
    }

    public function  getProfilEditAvancement() {
        //$edited = ;
        $allfields = get_object_vars($this);

        $edited = 0;
        foreach ($allfields as $field) {
            if (isset ($field)) {$edited++; }
        }
        return round (100*$edited / (count($allfields)));
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

    public function __toString(){
        return $this->username;
    }


    /**
     * Set biographie
     *
     * @param string $biographie
     * @return User
     */
    public function setBiographie($biographie)
    {
        $this->biographie = $biographie;

        return $this;
    }

    /**
     * Get biographie
     *
     * @return string 
     */
    public function getBiographie()
    {
        return $this->biographie;
    }

    /**
     * Set awards
     *
     * @param string $awards
     * @return User
     */
    public function setAwards($awards)
    {
        $this->awards = $awards;

        return $this;
    }

    /**
     * Get awards
     *
     * @return string 
     */
    public function getAwards()
    {
        return $this->awards;
    }

    /**
     * Set contrats
     *
     * @param string $contrats
     * @return User
     */
    public function setContrats($contrats)
    {
        $this->contrats = $contrats;

        return $this;
    }

    /**
     * Get contrats
     *
     * @return string 
     */
    public function getContrats()
    {
        return $this->contrats;
    }

    /**
     * Set agentPhone
     *
     * @param string $agentPhone
     * @return User
     */
    public function setAgentPhone($agentPhone)
    {
        $this->agentPhone = $agentPhone;

        return $this;
    }

    /**
     * Get agentPhone
     *
     * @return string 
     */
    public function getAgentPhone()
    {
        return $this->agentPhone;
    }

    public function setOptions($options){
        $this->options = $options;
    }

    public function getOptions(){
        return $this->options;
    }
}
