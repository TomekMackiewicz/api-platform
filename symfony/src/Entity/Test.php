<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 * @ORM\Table(name="test")
 * @ApiResource(
 *   collectionOperations={
 *     "get",
 *     "post"={"security"="is_granted('ROLE_ADMIN')"}
 *   },
 *   itemOperations={
 *     "get",
 *     "put"={"security"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     "patch"={"security"="is_granted('ROLE_ADMIN')"}
 *   }
 * )
 */
class Test
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private $summary;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     */
    private $nextSubmissionAfter;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     */
    private $ttl;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private $usePagination;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     */
    private $questionsPerPage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private $shuffleQuestions;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private $immediateAnswers;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private $restrictSubmissions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     */
    private $allowedSubmissions;

    // /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="survey")
    //  */
    // private $questions;

    // public function __construct()
    // {
    //     $this->questions = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNextSubmissionAfter(): ?int
    {
        return $this->nextSubmissionAfter;
    }

    public function setNextSubmissionAfter(?int $nextSubmissionAfter): self
    {
        $this->nextSubmissionAfter = $nextSubmissionAfter;

        return $this;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function setTtl(?int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getUsePagination(): ?bool
    {
        return $this->usePagination;
    }

    public function setUsePagination(bool $usePagination): self
    {
        $this->usePagination = $usePagination;

        return $this;
    }

    public function getQuestionsPerPage(): ?int
    {
        return $this->questionsPerPage;
    }

    public function setQuestionsPerPage(?int $questionsPerPage): self
    {
        $this->questionsPerPage = $questionsPerPage;

        return $this;
    }

    public function getShuffleQuestions(): ?bool
    {
        return $this->shuffleQuestions;
    }

    public function setShuffleQuestions(?bool $shuffleQuestions): self
    {
        $this->shuffleQuestions = $shuffleQuestions;

        return $this;
    }

    public function getImmediateAnswers(): ?bool
    {
        return $this->immediateAnswers;
    }

    public function setImmediateAnswers(?bool $immediateAnswers): self
    {
        $this->immediateAnswers = $immediateAnswers;

        return $this;
    }

    public function getRestrictSubmissions(): ?bool
    {
        return $this->restrictSubmissions;
    }

    public function setRestrictSubmissions(?bool $restrictSubmissions): self
    {
        $this->restrictSubmissions = $restrictSubmissions;

        return $this;
    }

    public function getAllowedSubmissions(): ?int
    {
        return $this->allowedSubmissions;
    }

    public function setAllowedSubmissions(?int $allowedSubmissions): self
    {
        $this->allowedSubmissions = $allowedSubmissions;

        return $this;
    }

    // public function getQuestions(): Collection
    // {
    //     return $this->questions;
    // }

    // public function addQuestion(Question $question): self
    // {
    //     $this->questions->add($question);

    //     return $this;
    // }

    // public function removeQuestion(Question $question): self
    // {
    //     $this->questions->removeElement($question);

    //     return $this;
    // }
}