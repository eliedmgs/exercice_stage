<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


 #[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
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

     
     #[ORM\Column(length: 4, nullable: true)]
    private ?string $extension;

    
     #[ORM\Column(nullable: true)]
    private ?int $ordre;

      
      #[ORM\Column(nullable: true)]
      #[Assert\File(
          maxSize: "5000000000000",
          mimeTypes: ["video/x-msvideo", "video/mpeg", "video/mp4"],
          mimeTypesMessage: "Merci de télécharger un fichier de type avi, mp4 ou mpeg"
      )]
    private ?string $file;

    
     #[ORM\ManyToMany(targetEntity: Apprenant::class, mappedBy: "videos")] 
    private Collection $apprenants;

    
    
     #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: "videos")] 
    private ?Module $module;

    
     #[ORM\OneToMany(targetEntity: ModuleQuestion::class, mappedBy: "video")] 
    private Collection $moduleQuestions;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
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
            $apprenant->addVideo($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->contains($apprenant)) {
            $this->apprenants->removeElement($apprenant);
            $apprenant->removeVideo($this);
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
            $moduleQuestion->setVideo($this);
        }

        return $this;
    }

    public function removeModuleQuestion(ModuleQuestion $moduleQuestion): self
    {
        if ($this->moduleQuestions->contains($moduleQuestion)) {
            $this->moduleQuestions->removeElement($moduleQuestion);
            // set the owning side to null (unless already changed)
            if ($moduleQuestion->getVideo() === $this) {
                $moduleQuestion->setVideo(null);
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
