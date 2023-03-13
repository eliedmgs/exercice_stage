<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormationRepository")
 */
class Formation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Le nom doit au moins contenir {{ limit }} caractères",
     *      maxMessage = "Le nom ne doit pas dépasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(
     *      message = "Le nom doit être renseigné"
     * )
     */
    private $nom;

     /**
     * @ORM\ManyToMany(targetEntity="Module", mappedBy="formations")
     */ 
    private $modules;

    /**
     * @ORM\ManyToOne(targetEntity="DomaineFormation", inversedBy="formations")
     * 
     * @ORM\JoinColumn(name="domaine_formation_id", referencedColumnName="id", onDelete="SET NULL")
     */ 
    private $domaineFormation;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
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

    /**
     * @return Collection|Module[]
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
            $module->addFormation($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            $module->removeFormation($this);
        }

        return $this;
    }

    public function getDomaineFormation(): ?DomaineFormation
    {
        return $this->domaineFormation;
    }

    public function setDomaineFormation(?DomaineFormation $domaineFormation): self
    {
        $this->domaineFormation = $domaineFormation;

        return $this;
    }
}
