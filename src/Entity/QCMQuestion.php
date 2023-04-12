<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass:"App\Repository\QCMQuestionRepository")]
class QCMQuestion
{
    /**
     * @var array BRANDS
     */
    public const DIFFICULTE = [
        self::DIFFICULTE_FACILE,
        self::DIFFICULTE_MOYENNE,
        self::DIFFICULTE_DIFFICILE,
    ];

     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length: 255)]  
     #[Assert\NotBlank(
           message: "La question doit être renseignée"
      )]
    private ?string $question = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulte = null;

     #[ORM\OneToMany(targetEntity: QCMReponse::class, mappedBy: "qcmQuestion", cascade: ["persist", "remove"])] 
    private Collection $qcmReponses;

    
    #[ORM\ManyToOne(targetEntity: QCM::class, inversedBy: "qcmQuestions")]
    private Collection $qcm;

    
    /**
     * @var string DIFFICULTE_FACILE
     */
    public const DIFFICULTE_FACILE = 'Facile';

    /**
     * @var string DIFFICULTE_MOYENNE
     */
    public const DIFFICULTE_MOYENNE = 'Moyenne';

    /**
     * @var string DIFFICULTE_DIFFICILE
     */
    public const DIFFICULTE_DIFFICILE = 'Difficile';


    public function __construct()
    {
        $this->qcmReponses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
            $qcmReponse->setQcmQuestion($this);
        }

        return $this;
    }

    public function removeQcmReponse(QCMReponse $qcmReponse): self
    {
        if ($this->qcmReponses->contains($qcmReponse)) {
            $this->qcmReponses->removeElement($qcmReponse);
            // set the owning side to null (unless already changed)
            if ($qcmReponse->getQcmQuestion() === $this) {
                $qcmReponse->setQcmQuestion(null);
            }
        }

        return $this;
    }

    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    public function setDifficulte(string $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    public function getQcm(): ?QCM
    {
        return $this->qcm;
    }

    public function setQcm(?QCM $qcm): self
    {
        $this->qcm = $qcm;

        return $this;
    }
    
}
