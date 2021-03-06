<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ExamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=ExamRepository::class)
 * @ORM\Table(name="exams")
 * @ApiResource(
 *   attributes={"normalization_context": {"groups"={"read"}, "enable_max_depth"=true}},
 *   collectionOperations={
 *     "get",
 *     "post"={"security"="is_granted('ROLE_ADMIN')"}
 *   },
 *   itemOperations={
 *     "get"={"security"="is_granted('ROLE_USER') or object.getRestrictSubmissions() == false"},
 *     "put"={"security"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     "patch"={"security"="is_granted('ROLE_ADMIN')"}
 *   },
 *   normalizationContext={"groups"={"read"}},
 *   denormalizationContext={"groups"={"post"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"title": "ipartial"})
 */
class Exam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private string $title = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private ?string $summary = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Groups({"read", "post"})
     */
    private ?int $duration = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Groups({"read", "post"})
     */
    private ?int $nextSubmissionAfter = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Groups({"read", "post"})
     */
    private ?int $ttl = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $usePagination = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Groups({"read", "post"})
     */
    private ?int $questionsPerPage = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $shuffleQuestions = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $immediateAnswers = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $restrictSubmissions = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Groups({"read", "post"})
     */
    private ?int $allowedSubmissions = null;

    /**
     * @var Question[] Questions for this exam.
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="exam", cascade={"persist", "remove"})
     * @Groups({"read", "post"})
     * @MaxDepth(1)
     */
    private iterable $questions;

    /**
     * @var Category[] Categories for this exam.
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="exams", cascade={"persist"})
     * @Groups({"read", "post"})
     * @MaxDepth(1)
     */
    private $categories;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
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

    public function setUsePagination(?bool $usePagination): self
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

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        $this->questions->add($question);
        $question->setExam($this);

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        $this->questions->removeElement($question);

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        return $this;
    }
    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);
        return $this;
    }
}