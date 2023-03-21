<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModuleReponseRepository::class)]
class ModuleReponse
{
     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length: 255)]
     #[Assert\NotBlank(
           message: "La réponse doit être renseignée"
      )]
    private ?string $reponse;


    
    #[ORM\ManyToOne(targetEntity: ModuleQuestion::class, inversedBy: "moduleReponses")] 
    private ?ModuleQuestion $moduleQuestion;

    
    #[ORM\ManyToOne(targetEntity: Formateur::class, inversedBy: "moduleReponses")] 
    private ?Formateur $formateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getModuleQuestion(): ?ModuleQuestion
    {
        return $this->moduleQuestion;
    }

    public function setModuleQuestion(?ModuleQuestion $moduleQuestion): self
    {
        $this->moduleQuestion = $moduleQuestion;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }

}
