<?php

namespace Panier\EcommerceBundle\Entity;

use AppBundle\Entity\Mets;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="Panier\EcommerceBundle\Repository\CommandeRepository")
 * @UniqueEntity("owner")
 */
class Commande
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="owner", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $owner;


    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Mets", inversedBy="commandes")
     * @var Collection
     */
    private $mets;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param string $owner
     *
     * @return Commande
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->id = Uuid::uuid4()->toString();
    }

    /**
     * Remove met
     *
     * @param \AppBundle\Entity\Mets $met
     */
    public function removeMet(\AppBundle\Entity\Mets $met)
    {
        if (!$this->mets->contains($met)) {
            return;
        }

        $this->mets->removeElement($met);
        $met->removeCommande($this);
    }

    /**
     * Add met
     *
     * @param \AppBundle\Entity\Mets $met
     *
     * @return Commande
     */
    public function addMet(\AppBundle\Entity\Mets $met)
    {
        if ($this->mets->contains($met)) {
            // Do nothing if its already part of our collection
            return;
        }
        $this->mets[] = $met;
        $met->addCommande($this);

        return $this;
    }

    /**
     * Get mets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMets()
    {
        return $this->mets;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Commande
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }
}
