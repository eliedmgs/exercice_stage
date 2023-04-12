<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\AbstractTypeExtension;


#[ORM\Entity(repositoryClass:"App\Repository\ModuleQuestionRepository")]
class ModuleQuestion
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
          message: "La question doit être renseignée"
      )]
    private ?string $question = null;

    
    #[ORM\ManyToOne(targetEntity: PDF::class, inversedBy: "moduleQuestions")]
    private ?PDF $pdf;

    
    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: "moduleQuestions")] 
    private ?Video $video;

    
    #[ORM\OneToMany(targetEntity: ModuleReponse::class, mappedBy: "moduleQuestion")] 
    private Collection $moduleReponses;

    
    #[ORM\ManyToOne(targetEntity: Apprenant::class, inversedBy: "moduleQuestions")] 
    private ?Apprenant $apprenant;

    public function __construct()
    {
        $this->moduleReponse = new ArrayCollection();
        $this->moduleReponses = new ArrayCollection();
    }

   
    



    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->question;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getPdf(): ?PDF
    {
        return $this->pdf;
    }

    public function setPdf(?PDF $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection|ModuleReponse[]
     */
    public function getModuleReponse(): Collection
    {
        return $this->moduleReponse;
    }

    public function addModuleReponse(ModuleReponse $moduleReponse): self
    {
        if (!$this->moduleReponse->contains($moduleReponse)) {
            $this->moduleReponse[] = $moduleReponse;
            $moduleReponse->setModuleQuestion($this);
        }

        return $this;
    }

    public function removeModuleReponse(ModuleReponse $moduleReponse): self
    {
        if ($this->moduleReponse->contains($moduleReponse)) {
            $this->moduleReponse->removeElement($moduleReponse);
            // set the owning side to null (unless already changed)
            if ($moduleReponse->getModuleQuestion() === $this) {
                $moduleReponse->setModuleQuestion(null);
            }
        }

        return $this;
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
     * @return Collection|ModuleReponse[]
     */
    public function getModuleReponses(): Collection
    {
        return $this->moduleReponses;
    }

    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): self
    {
        $this->apprenant = $apprenant;

        return $this;
    }
}
