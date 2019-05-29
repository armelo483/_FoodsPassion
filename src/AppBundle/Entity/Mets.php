<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Mets
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="mets", uniqueConstraints={@ORM\UniqueConstraint(name="metscol_UNIQUE", columns={"metscol"})})
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Mets
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idmets", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmets;

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
     * @var File
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
     * @ORM\Column(name="libelle", type="string", length=15, nullable=true)
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
     * @ORM\PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        //$this->updatedAt = new \DateTime();
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



}

