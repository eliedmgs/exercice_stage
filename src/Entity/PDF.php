<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PDFRepository")
 */
class PDF
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
     * * @Assert\Length(
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
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $extension;

    /**
     * 
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = { "application/pdf" },
     *     mimeTypesMessage = "Merci de télécharger un fichier de type pdf"
     * )
     */
    private $file;

    /** 
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @ORM\ManyToMany(targetEntity="Apprenant", mappedBy="pdfs")
     */ 
    private $apprenants;

     /**
     * @ORM\ManyToOne(targetEntity="Module", inversedBy="pdfs")
     */ 
    private $module;

    /**
     * @ORM\OneToMany(targetEntity="ModuleQuestion", mappedBy="pdf")
     */ 
    private $moduleQuestions;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
        $this->pdf = new ArrayCollection();
        $this->moduleQuestions = new ArrayCollection();
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
            $apprenant->addPdf($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->contains($apprenant)) {
            $this->apprenants->removeElement($apprenant);
            $apprenant->removePdf($this);
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

    /**
     * @return Collection|moduleQuestion[]
     */
    public function getPdf(): Collection
    {
        return $this->pdf;
    }

    public function addPdf(moduleQuestion $pdf): self
    {
        if (!$this->pdf->contains($pdf)) {
            $this->pdf[] = $pdf;
            $pdf->setPdf($this);
        }

        return $this;
    }

    public function removePdf(moduleQuestion $pdf): self
    {
        if ($this->pdf->contains($pdf)) {
            $this->pdf->removeElement($pdf);
            // set the owning side to null (unless already changed)
            if ($pdf->getPdf() === $this) {
                $pdf->setPdf(null);
            }
        }

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): self
    {
        $this->file = $file;

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
            $moduleQuestion->setPdf($this);
        }

        return $this;
    }

    public function removeModuleQuestion(ModuleQuestion $moduleQuestion): self
    {
        if ($this->moduleQuestions->contains($moduleQuestion)) {
            $this->moduleQuestions->removeElement($moduleQuestion);
            // set the owning side to null (unless already changed)
            if ($moduleQuestion->getPdf() === $this) {
                $moduleQuestion->setPdf(null);
            }
        }

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
