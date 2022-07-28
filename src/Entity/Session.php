<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\AST\Functions\LengthFunction;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPlace;

    /**
     * @ORM\ManyToMany(targetEntity=Intern::class, inversedBy="sessions")
     */
    private $interns;

    /**
     * @ORM\ManyToOne(targetEntity=Formation::class, inversedBy="sessions")
     */
    private $formation;

    /**
     * @ORM\OneToMany(targetEntity=Program::class, mappedBy="session")
     */
    private $programs;

    public function __construct()
    {
        $this->interns = new ArrayCollection();
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    // fonction pour afficher le temps de foramtion au format date jour mois année 
    public function getDateDays()
    {
        $days = date_diff($this->dateStart,$this->dateEnd);
        return $days->format("%d jours /%m mois /%Y années");
    }

    // fonction pour afficher le nombre de jours de la formation en nombre de jour 
    public function getTotalDaysSession()
    {
       $start  = $this->dateStart->getTimestamp();
       $end  = $this->dateEnd->getTimestamp();
       $days = $end - $start;
       $result = (int)($days /( 60 * 60 * 24 ));
       return $result;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    // fonction pour afficher le nombre de place restante dans la session 
    public function getRemainingPlaces(){
       $reserved =  $this->nbPlace - count($this->interns);
    //    if($reserved < 0){
    //     echo "<p class='bg-danger'>Erreur la limite de stagiaire prévue dans la session à été dépasser</p>";
    //    }else{
           return $reserved;
    //    }
    }
    // fonction pour afficher le nombre de place réserve pour la session 
    public function getNbPlaceReserved(){
        $reserved =  $this->nbPlace - count($this->interns);
        $remaining =  $this->nbPlace - $reserved;
        // if($remaining > $this->nbPlace){
        //     echo "<p class='bg-danger'>Erreur la limite de stagiaire prévue dans la session à été dépasser ".$remaining." séléctionner, ".$this->nbPlace." de prévue.</p>";
        // }else{
        return $remaining;
        // }
    }
    /**
     * @return Collection<int, Intern>
     */
    public function getInterns(): Collection
    {
        return $this->interns;
    }

    public function addIntern(Intern $intern): self
    {
        if (!$this->interns->contains($intern)) {
            $this->interns[] = $intern;
        }
        
        return $this;
    }

    public function removeIntern(Intern $intern): self
    {
        $this->interns->removeElement($intern);

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    // fonction pour afficher le nombre de jour au total de tout les modules ajouter à la session 
    public function getTotalDaysFormation(){
        $days = 0 ;  
        foreach ($this->programs as $program) {
            $days += $program->getNbDays();                    
        }
        return $days ;
    }
    
    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setSession($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getSession() === $this) {
                $program->setSession(null);
            }
        }

        return $this;
    }

}
