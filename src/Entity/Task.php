<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\Table(name="tasks")
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(
     * message = "Merci de rentrer un nom de tache"
     * )
     * * @Assert\Length(
     *         max = 150,
     *         maxMessage = "Your task name cannot be longer than {{ limit }} characters")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     * message = "Merci de rentrer une description"
     * )
     * @Assert\Length(
     *         max = 250,
     *         maxMessage = "Your description cannot be longer than {{ limit }} characters")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * 
     */
    private $start_date;

    /**
     * @ORM\Column(type="date")
     *
     */
    private $end_date;

    /**
     * @ORM\ManyToOne(targetEntity=project::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
