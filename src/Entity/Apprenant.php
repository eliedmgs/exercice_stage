<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


 #[ORM\Entity(repositoryClass: ApprenantRepository::class)]
 
class Apprenant
{
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\OneToOne(inversedBy: "apprenant", cascade:["persist"])]
    private ?Utilisateur $utilisateur;

    
    #[ORM\ManyToMany(targetEntity: PDF::class, inversedBy: "apprenants")]
    private Collection $pdfs;

    
    #[ORM\ManyToMany(targetEntity: Video::class, inversedBy: "apprenants")]
    private Collection $videos;

    
    #[ORM\ManyToMany(targetEntity: QCMReponse::class, inversedBy: "apprenants")]
    private Collection $qcmReponses;
    
    
    #[ORM\ManyToMany(targetEntity: Module::class, inversedBy: "apprenants")] 
    private Collection $modules;

    
    #[ORM\OneToMany(targetEntity: ModuleQuestion::class, mappedBy: "apprenant")] 
    private Collection $moduleQuestions;

    public function __construct()
    {
        $this->pdfs = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->qcmReponses = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->moduleQuestions = new ArrayCollection();
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
     * @return Collection|PDF[]
     */
    public function getPdfs(): Collection
    {
        return $this->pdfs;
    }

    public function addPdf(PDF $pdf): self
    {
        if (!$this->pdfs->contains($pdf)) {
            $this->pdfs[] = $pdf;
        }

        return $this;
    }

    public function removePdf(PDF $pdf): self
    {
        if ($this->pdfs->contains($pdf)) {
            $this->pdfs->removeElement($pdf);
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
        }

        return $this;
    }

    /**
     * @return Collection|QCMReponse[]
     */
    public function getQcmReponses(): Collection
    {
        return $this->qcmReponses;
    }

    public function addQcmReponse(QCMReponse $qcmReponse): self
    {
        if (!$this->qcmReponses->contains($qcmReponse)) {
            $this->qcmReponses[] = $qcmReponse;
        }

        return $this;
    }

    public function removeQcmReponse(QCMReponse $qcmReponse): self
    {
        if ($this->qcmReponses->contains($qcmReponse)) {
            $this->qcmReponses->removeElement($qcmReponse);
        }

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
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
        }

        return $this;
    }

    /**
     * @return Collection|ModuleQuestion[]
     */
    public function getModuleQuestions(): Collection
    {
        return $this->moduleQuestions;
    }

    public function addModuleQuestion(ModuleQuestion $moduleQuestion): self
    {
        if (!$this->moduleQuestions->contains($moduleQuestion)) {
            $this->moduleQuestions[] = $moduleQuestion;
            $moduleQuestion->setApprenant($this);
        }

        return $this;
    }

    public function removeModuleQuestion(ModuleQuestion $moduleQuestion): self
    {
        if ($this->moduleQuestions->contains($moduleQuestion)) {
            $this->moduleQuestions->removeElement($moduleQuestion);
            // set the owning side to null (unless already changed)
            if ($moduleQuestion->getApprenant() === $this) {
                $moduleQuestion->setApprenant(null);
            }
        }

        return $this;
    }
}
