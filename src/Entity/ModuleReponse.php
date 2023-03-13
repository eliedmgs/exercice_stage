<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModuleReponseRepository")
 */
class ModuleReponse
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
     * @Assert\NotBlank(
     *      message = "La réponse doit être renseignée"
     * )
     */
    private $reponse;


     /**
     * @ORM\ManyToOne(targetEntity="ModuleQuestion", inversedBy="moduleReponses")
     */ 
    private $moduleQuestion;

    /**
     * @ORM\ManyToOne(targetEntity="Formateur", inversedBy="moduleReponses")
     */ 
    private $formateur;

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
