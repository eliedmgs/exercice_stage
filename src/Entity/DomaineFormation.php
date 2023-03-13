<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomaineFormationRepository")
 */
class DomaineFormation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le nom doit au moins contenir {{ limit }} caractères",
     *      maxMessage = "Le nom ne doit pas dépasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(
     *      message = "Le nom doit être renseignée"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */

    private $extension;

    /**
    * @ORM\Column(type="string", nullable=true)
    * @Assert\File(
    *     maxSize = "5M",
    *     mimeTypes = {"image/jpeg","image/png","image/jpg"},
    *     mimeTypesMessage = "Une image valide doit être requis"
    * )
    */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="Formation", mappedBy="domaineFormation")
     */ 
    private $formations;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->setDomaineFormation($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->contains($formation)) {
            $this->formations->removeElement($formation);
            // set the owning side to null (unless already changed)
            if ($formation->getDomaineFormation() === $this) {
                $formation->setDomaineFormation(null);
            }
        }

        return $this;
    }
}
