<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 25/06/2014
 * Time: 23:25
 */

namespace LTM\PortailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"compte" = "LTM\PortailBundle\Entity\CCompte",
 * "action" = "LTM\PortailBundle\Entity\CAction",
 * "activity"="LTM\PortailBundle\Entity\CActivity",
 * "casting" = "LTM\PortailBundle\Entity\CCasting",
 * "castingtarget" = "LTM\PortailBundle\Entity\CCastingTarget",
 * "dance" = "LTM\PortailBundle\Entity\CDance",
 * "calendarevent" = "LTM\PortailBundle\Entity\CCalendarEvent",
 * "news" = "LTM\PortailBundle\Entity\CNews",
 * "relation" = "LTM\PortailBundle\Entity\CRelation",
 * "status" = "LTM\PortailBundle\Entity\CStatus"})
 * @ORM\Entity(repositoryClass="LTM\PortailBundle\Entity\StatusRepository")
 */
class CPublish
{
    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=255)
     * @Assert\NotBlank()
     */
    private $nom;

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
     * Set nom
     *
     * @param string $nom
     * @return Categorie
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return CPublish
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
