<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Panier\EcommerceBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * Mets
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="mets", uniqueConstraints={@ORM\UniqueConstraint(name="metscol_UNIQUE", columns={"metscol"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MetsRepository")
 * @Vich\Uploadable
 */
class Mets
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",  nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=45, nullable=true)
     */
    private $imageUrl;

    /**
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="imageUrl")
     * @var File $imageFile
     */
    private $imageFile;


    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @var \DateTime
     */
    private $updatedAt ;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=75, nullable=true)
     */
    private $libelle;


    /**
     * @var string
     *
     * @ORM\Column(name="metscol", type="string", length=45, nullable=true)
     */
    private $metscol;

    /**
     * @var string
     *
     * @ORM\Column(name="notation_moyenne", type="decimal", precision=2, scale=0, nullable=true)
     */
    private $notationMoyenne;

    /**
     * @var string
     *
     * @ORM\Column(name="prix", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="stock", type="integer", nullable=true, options={"default" : 1})
     */
    private $stock;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Panier\EcommerceBundle\Entity\Commande", cascade={"persist"},mappedBy="mets")
     * @ORM\JoinTable(name="commande_mets",
     *      joinColumns={@ORM\JoinColumn(name="met_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="commande_id", referencedColumnName="id")}
     *      )
     * @var Collection
     */

    private $commandes;

    /**
     * @return mixed
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * @param mixed $commandes
     */
    public function setCommandes($commandes)
    {
        $this->commandes = $commandes;
    }


    /**
     * @ORM\PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        $this->updatedAt = new \DateTime();
    }


    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @return int
     */
    public function getIdmets()
    {
        return $this->idmets;
    }

    /**
     * @param int $idmets
     */
    public function setIdmets($idmets)
    {
        $this->idmets = $idmets;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return string
     */
    public function getMetscol()
    {
        return $this->metscol;
    }

    /**
     * @param string $metscol
     */
    public function setMetscol($metscol)
    {
        $this->metscol = $metscol;
    }

    /**
     * @return string
     */
    public function getNotationMoyenne()
    {
        return $this->notationMoyenne;
    }

    /**
     * @param string $notationMoyenne
     */
    public function setNotationMoyenne($notationMoyenne)
    {
        $this->notationMoyenne = $notationMoyenne;
    }

    /**
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param string $prix
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /**
     * Add commande
     *
     * @param \Panier\EcommerceBundle\Entity\Commande $commande
     *
     * @return Mets
     */
    public function addCommande(\Panier\EcommerceBundle\Entity\Commande $commande)
    {
        if ($this->commandes->contains($commande)) {
            // Do nothing if its already part of our collection
            return;
        }

        $this->commandes[] = $commande;
        $commande->addMet($this);
        return $this;
    }

    /**
     * Remove commande
     *
     * @param \Panier\EcommerceBundle\Entity\Commande $commande
     */
    public function removeCommande(\Panier\EcommerceBundle\Entity\Commande $commande)
    {
        if (!$this->commandes->contains($commande)) {
            return;
        }

        $this->commandes->removeElement($commande);
        $commande->removeMet($this);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * Set qte
     *
     * @param integer $qte
     *
     * @return Mets
     */
    public function setQte($qte)
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte
     *
     * @return integer
     */
    public function getQte()
    {
        return $this->qte;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Mets
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }
}
