<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
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

    
    #[ORM\Column(length:255)]
    #[Assert\NotBlank(
          message: "La description doit être renseignée"
     )]
    private ?string $description = null;

    
    #[ORM\Column(length: 255)] 
    #[Assert\NotBlank(
          message: "La mini-description doit être renseignée"
     )]
    private ?string $mini_description = null;

    
    #[ORM\Column(nullable: true)]
    private ?string $extension;

    #[ORM\Column(nullable: true)]
    #[Assert\File(
         maxSize: "5M",
         mimeTypes: ["image/jpeg","image/png","image/jpg"],
         mimeTypesMessage: "Une image valide doit être requis"
     )]
    private ?string $image;

    
    #[ORM\ManyToMany(targetEntity: Apprenant::class, mappedBy: "modules", cascade: ["persist", "remove"])]
    private Collection $apprenants;


    #[ORM\ManyToOne(targetEntity: Formateur::class, inversedBy: "modules")] 
    private ?Formateur $formateur;

    
    #[ORM\OneToMany(targetEntity: QCM::class, mappedBy: "module", cascade: ["persist", "remove"])]
    private Collection $qcms;

     
    #[ORM\ManyToMany(targetEntity:Formation::class, inversedBy: "modules")]
    private Collection $formations;


    #[ORM\OneToMany(targetEntity: PDF::class, mappedBy: "module", cascade: ["persist", "remove"])]
    private Collection $pdfs;

    
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: "module", cascade: ["persist", "remove"])] 
    private Collection $videos;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
        $this->qcms = new ArrayCollection();
        $this->formations = new ArrayCollection();
        $this->pdfs = new ArrayCollection();
        $this->videos = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $apprenant->addModule($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->contains($apprenant)) {
            $this->apprenants->removeElement($apprenant);
            $apprenant->removeModule($this);
        }

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

    /**
     * @return Collection|QCM[]
     */
    public function getQcms(): Collection
    {
        return $this->qcms;
    }

    public function addQcm(QCM $qcm): self
    {
        if (!$this->qcms->contains($qcm)) {
            $this->qcms[] = $qcm;
            $qcm->setModule($this);
        }

        return $this;
    }

    public function removeQcm(QCM $qcm): self
    {
        if ($this->qcms->contains($qcm)) {
            $this->qcms->removeElement($qcm);
            // set the owning side to null (unless already changed)
            if ($qcm->getModule() === $this) {
                $qcm->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->contains($formation)) {
            $this->formations->removeElement($formation);
        }

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
            $pdf->setModule($this);
        }

        return $this;
    }

    public function removePdf(PDF $pdf): self
    {
        if ($this->pdfs->contains($pdf)) {
            $this->pdfs->removeElement($pdf);
            // set the owning side to null (unless already changed)
            if ($pdf->getModule() === $this) {
                $pdf->setModule(null);
            }
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
            $video->setModule($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getModule() === $this) {
                $video->setModule(null);
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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getMiniDescription(): ?string
    {
        return $this->mini_description;
    }

    public function setMiniDescription(?string $mini_description): self
    {
        $this->mini_description = $mini_description;

        return $this;
    }
}
