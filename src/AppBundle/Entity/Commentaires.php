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
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="commentaires")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentairesRepository")
 */
class Commentaires
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="notation", type="string", type="decimal", precision=2, scale=0, nullable=true)
     */
    protected $notation;

    /**
     * @ORM\Column(name="auteur", type="string", nullable=true)
     */
    protected $auteur;

    /**
     * @ORM\Column(name="titrecommentaires", type="string", nullable=true)
     */
    protected $titreCommentaires;

    /**
     * @ORM\Column(name="commentaires", type="text", nullable=true)
     */
    protected $Commentaires;

    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="mets", inversedBy="commentaires")
     * @ORM\JoinColumn(name="met_id", referencedColumnName="id")
     */
    private $mets;

    /**
     * @return mixed
     */
    public function getMets()
    {
        return $this->mets;
    }

    /**
     * @param mixed $mets
     */
    public function setMets($mets)
    {
        $this->mets = $mets;
    }

    /**
     * @return mixed
     */
    public function getTitreCommentaires()
    {
        return $this->titreCommentaires;
    }

    /**
     * @param mixed $titreCommentaires
     */
    public function setTitreCommentaires($titreCommentaires)
    {
        $this->titreCommentaires = $titreCommentaires;
    }


    /**
     * @return mixed
     */
    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * @param mixed $notation
     */
    public function setNotation($notation)
    {
        $this->notation = $notation;
    }

    /**
     * @return mixed
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * @param mixed $auteur
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->Commentaires;
    }

    /**
     * @param mixed $Commentaires
     */
    public function setCommentaires($Commentaires)
    {
        $this->Commentaires = $Commentaires;
    }






}