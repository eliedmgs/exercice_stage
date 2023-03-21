<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

  #[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
  #[UniqueEntity(fields:["email"], message: "There is already an account with this email")]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{    
     #[ORM\Id]
     #[ORM\GeneratedValue]
     #[ORM\Column]
    private ?int $id = null;

    
     #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    
      #[ORM\Column(length: 255)]
      #[Assert\Length(
           min: 2,
           max: 50,
           minMessage: "Le nom doit au moins contenir {{ limit }} caractères",
           maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères"
      )]
      #[Assert\NotBlank(
           message: "Le nom doit être renseigné"
      )]
    private ?string $nom = null;

    
     #[ORM\Column(length: 255)]
     #[Assert\Length(
           min: 2,
           max: 50,
           minMessage: "Le prénom doit au moins contenir {{ limit }} caractères",
           maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères"
      )]
     #[Assert\NotBlank(
           message: "Le prénom doit être renseignée"
      )]
    private ?string $prenom = null;

    
    #[ORM\Column(type: "json")]
    private $roles = [];

    
     #[ORM\Column]
    private ?string $password = null;

    
    #[ORM\OneToOne(targetEntity: Formateur::class, mappedBy: "utilisateur", cascade: ["persist", "remove"])] 
    private ?Formateur $formateur;

    
    #[ORM\OneToOne(targetEntity: Apprenant::class, mappedBy: "utilisateur", cascade: ["persist", "remove"])] 
    private ?Apprenant $apprenant;

    

    public function __construct()
    {
        $this->moduleReponse = new ArrayCollection();
        $this->moduleQuestions = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

        // set (or unset) the owning side of the relation if necessary
        $newUtilisateur = null === $formateur ? null : $this;
        if ($formateur->getUtilisateur() !== $newUtilisateur) {
            $formateur->setUtilisateur($newUtilisateur);
        }

        return $this;
    }

    public function getApprenant(): ?Apprenant
    {
        return $this->apprenant;
    }

    public function setApprenant(?Apprenant $apprenant): self
    {
        $this->apprenant = $apprenant;

        // set (or unset) the owning side of the relation if necessary
        $newUtilisateur = null === $apprenant ? null : $this;
        if ($apprenant->getUtilisateur() !== $newUtilisateur) {
            $apprenant->setUtilisateur($newUtilisateur);
        }

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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
            $moduleReponse->setModuleReponses($this);
        }

        return $this;
    }

    public function removeModuleReponse(ModuleReponse $moduleReponse): self
    {
        if ($this->moduleReponse->contains($moduleReponse)) {
            $this->moduleReponse->removeElement($moduleReponse);
            // set the owning side to null (unless already changed)
            if ($moduleReponse->getModuleReponses() === $this) {
                $moduleReponse->setModuleReponses(null);
            }
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
            $moduleQuestion->setUtilisateur($this);
        }

        return $this;
    }

    public function removeModuleQuestion(ModuleQuestion $moduleQuestion): self
    {
        if ($this->moduleQuestions->contains($moduleQuestion)) {
            $this->moduleQuestions->removeElement($moduleQuestion);
            // set the owning side to null (unless already changed)
            if ($moduleQuestion->getUtilisateur() === $this) {
                $moduleQuestion->setUtilisateur(null);
            }
        }

        return $this;
    }
}
