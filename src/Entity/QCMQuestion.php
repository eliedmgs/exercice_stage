<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QCMQuestionRepository")
 */
class QCMQuestion
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
     *      message = "La question doit être renseignée"
     * )
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="QCMReponse", mappedBy="qcmQuestion", cascade={"persist", "remove"})
     */ 
    private $qcmReponses;

    /**
     * @ORM\ManyToOne(targetEntity="QCM", inversedBy="qcmQuestions")
     */ 
    private $qcm;

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
