<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DomaineFormationRepository::class)] 
class DomaineFormation
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length:255)]
     #[Assert\Length(
          min: 2,
          max: 50,
          minMessage: "Le nom doit au moins contenir {{ limit }} caractères",
          maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères"
     )]
     #[Assert\NotBlank(
          message: "Le nom doit être renseignée",
     )]
    private ?string $nom = null;

    
    #[ORM\Column(nullable: true)]
    private ?string $extension;


    #[ORM\Column(nullable: true)]
    #[Assert\File(
         maxSize: "5M",
         mimeTypes: ["image/jpeg","image/png","image/jpg"],
         mimeTypesMessage: "Une image valide doit être requis"
     )]
    private ?string $image;

    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: "domaineFormation")] 
    private Collection $formations;

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
