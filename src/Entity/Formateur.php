<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormateurRepository")
 */
class Formateur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Utilisateur", inversedBy="formateur", cascade={"persist"})
     */ 
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity="Module", mappedBy="formateur")
     */ 
    private $modules;

    /**
     * @ORM\OneToMany(targetEntity="ModuleReponse", mappedBy="formateur")
     */ 
    private $moduleReponses;


    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->moduleReponses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

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
            $module->setFormateur($this);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
            // set the owning side to null (unless already changed)
            if ($module->getFormateur() === $this) {
                $module->setFormateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModuleReponse[]
     */
    public function getModuleReponses(): Collection
    {
        return $this->moduleReponses;
    }

    public function addModuleReponse(ModuleReponse $moduleReponse): self
    {
        if (!$this->moduleReponses->contains($moduleReponse)) {
            $this->moduleReponses[] = $moduleReponse;
            $moduleReponse->setFormateur($this);
        }

        return $this;
    }

    public function removeModuleReponse(ModuleReponse $moduleReponse): self
    {
        if ($this->moduleReponses->contains($moduleReponse)) {
            $this->moduleReponses->removeElement($moduleReponse);
            // set the owning side to null (unless already changed)
            if ($moduleReponse->getFormateur() === $this) {
                $moduleReponse->setFormateur(null);
            }
        }

        return $this;
    }
}
