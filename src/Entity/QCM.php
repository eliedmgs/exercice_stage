<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:"App\Repository\QCMRepository")]
class QCM
{
     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length: 255)] 
     #[Assert\Length(
           min: 2,
           max: 50,
           minMessage: "Le nom doit au moins contenir {{ limit }} caractères",
           maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères"
      )]
     #[Assert\NotBlank(
           message: "Le nom doit être renseignée"
      )]
    private ?string $nom = null;

     
     #[ORM\Column]
    private ?int $ordre = null;

    
     #[ORM\OneToMany(targetEntity: QCMQuestion::class, mappedBy: "qcm", cascade: ["persist", "remove"])] 
    private Collection $qcmQuestions;
    
    
     #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: "qcms")] 
    private ?Module $module;

    public function __construct()
    {
        $this->qcmQuestions = new ArrayCollection();
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
     * @return Collection|QCMQuestion[]
     */
    public function getQcmQuestions(): Collection
    {
        return $this->qcmQuestions;
    }

    public function addQcmQuestion(QCMQuestion $qcmQuestion): self
    {
        if (!$this->qcmQuestions->contains($qcmQuestion)) {
            $this->qcmQuestions[] = $qcmQuestion;
            $qcmQuestion->setQcm($this);
        }

        return $this;
    }

    public function removeQcmQuestion(QCMQuestion $qcmQuestion): self
    {
        if ($this->qcmQuestions->contains($qcmQuestion)) {
            $this->qcmQuestions->removeElement($qcmQuestion);
            // set the owning side to null (unless already changed)
            if ($qcmQuestion->getQcm() === $this) {
                $qcmQuestion->setQcm(null);
            }
        }

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }
}
