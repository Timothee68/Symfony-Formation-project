<?php

namespace App\Entity;



// use Assert\Regex;
// use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
    * @var string The hashed password
    * @ORM\Column(type="string")
    * 
    */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pseudo;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user")
     */
    private $messagesArray;

    /**
     * @ORM\OneToMany(targetEntity=MessageMail::class, mappedBy="sender", orphanRemoval=true)
     */
    private $sent;

    /**
     * @ORM\OneToMany(targetEntity=MessageMail::class, mappedBy="recipient", orphanRemoval=true)
     */
    private $received;


    public function __construct()
    {
        $this->messagesArray = new ArrayCollection(); // fil actualiter 
        // $this->users = new ArrayCollection();
        $this->sent = new ArrayCollection(); // collection de message envoyer 
        $this->received = new ArrayCollection(); // collection de message recus 
    }




    public function getNbReceivedNotRead()
    {
        $received = $this->received;
        $count=0;
        foreach ($received as $message) {
            if($message->isIsRead()== false){
                $count++;
            }
        }
            return $count;
    }
    
    public function getNbSent()
    {
            $sent = count($this->sent) ;
            return "\"".$sent."\" ";
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
   
    /**
     * @return Collection<int, Message>
     */
    public function getMessagesArray(): Collection
    {
        return $this->messagesArray;
    }

    public function addMessagesArray(Message $messagesArray): self
    {
        if (!$this->messagesArray->contains($messagesArray)) {
            $this->messagesArray[] = $messagesArray;
            $messagesArray->setUser($this);
        }

        return $this;
    }

    public function removeMessagesArray(Message $messagesArray): self
    {
        if ($this->messagesArray->removeElement($messagesArray)) {
            // set the owning side to null (unless already changed)
            if ($messagesArray->getUser() === $this) {
                $messagesArray->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageMail>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(MessageMail $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(MessageMail $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageMail>
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(MessageMail $received): self
    {
        if (!$this->received->contains($received)) {
            $this->received[] = $received;
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(MessageMail $received): self
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getPseudo();
    }
   
}
