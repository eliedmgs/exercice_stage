<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QCMReponseRepository::class)] 
class QCMReponse
{
     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length: 255)]
      
      #[Assert\NotBlank(
           message: "La réponse doit être renseignée"
      )]
    private ?string $reponse = null;

    
    #[ORM\Column]
    private ?bool $bonneReponse;

    
    #[ORM\ManyToMany(targetEntity: Apprenant::class, mappedBy: "qcmReponses")] 
    private Collection $apprenants;

    
    
    #[ORM\ManyToOne(targetEntity: QCMQuestion::class, inversedBy: "qcmReponses")] 
    private ?QCMQuestion $qcmQuestion = null;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->reponse;
    }



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

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants[] = $apprenant;
            $apprenant->addQcmReponse($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->contains($apprenant)) {
            $this->apprenants->removeElement($apprenant);
            $apprenant->removeQcmReponse($this);
        }

        return $this;
    }

    public function getQcmQuestion(): ?QCMQuestion
    {
        return $this->qcmQuestion;
    }

    public function setQcmQuestion(?QCMQuestion $qcmQuestion): self
    {
        $this->qcmQuestion = $qcmQuestion;

        return $this;
    }

    public function getBonneReponse(): ?bool
    {
        return $this->bonneReponse;
    }

    public function setBonneReponse(bool $bonneReponse): self
    {
        $this->bonneReponse = $bonneReponse;

        return $this;
    }

    public function isBonneReponse(): ?bool
    {
        return $this->bonneReponse;
    }
}
