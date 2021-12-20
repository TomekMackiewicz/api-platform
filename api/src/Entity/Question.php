<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Exam;
use App\Entity\Answer;
use App\Entity\MediaObject;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 * @ORM\Table(name="questions")
 * @ApiResource(
 *   collectionOperations={
 *     "get"={"security"="is_granted('ROLE_ADMIN')"},
 *     "post"={"security"="is_granted('ROLE_ADMIN')"}
 *   },
 *   itemOperations={
 *     "get"={"security"="is_granted('ROLE_ADMIN')"},
 *     "put"={"security"="is_granted('ROLE_ADMIN')"},
 *     "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     "patch"={"security"="is_granted('ROLE_ADMIN')"}
 *   }
 * )
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private string $label = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private ?string $description = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private string $type = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     * @Groups({"read", "post"})
     */
    private ?string $hint = '';

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $isRequired = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     * @Groups({"read", "post"})
     */
    private ?bool $shuffleAnswers = null;

    /**
     * @var Exam
     * @ORM\ManyToOne(targetEntity="App\Entity\Exam", inversedBy="questions")
     */
    private ?Exam $exam = null;

    /**
     * @var Answer[] Answers for this question.
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question", cascade={"persist", "remove"})
     * @Groups({"read", "post"})
     */
    private iterable $answers = [];

    /**
     * @var MediaObject[] MediaObjects for this question.
     * @ORM\OneToMany(targetEntity="App\Entity\MediaObject", mappedBy="question", cascade={"persist", "remove"})
     * @Groups({"read", "post"})
     * @ApiProperty(iri="http://schema.org/image")
     */
    private iterable $images = [];

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function setHint(?string $hint): self
    {
        $this->hint = $hint;

        return $this;
    }

    public function getIsRequired(): ?bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(?bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    public function getShuffleAnswers(): ?bool
    {
        return $this->shuffleAnswers;
    }

    public function setShuffleAnswers(?bool $shuffleAnswers): self
    {
        $this->shuffleAnswers = $shuffleAnswers;

        return $this;
    }

    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        $this->answers->add($answer);
        $answer->setQuestion($this);

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        $this->answers->removeElement($answer);

        return $this;
    }

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaObject $image): self
    {
        $this->images->add($image);
        $image->setQuestion($this);

        return $this;
    }

    public function removeImage(MediaObject $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
}