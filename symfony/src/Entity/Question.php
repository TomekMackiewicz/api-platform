<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 * @ORM\Table(name="questions")
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
     */
    private string $label = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private ?string $description = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private string $type = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(
     *   type="string",
     *   message="validation.not_string"
     * )
     */
    private ?string $hint = '';

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private ?bool $isRequired = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(
     *   type="bool",
     *   message="validation.not_bool"
     * )
     */
    private ?bool $shuffleAnswers = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Exam", inversedBy="questions")
     */
    private $exam;

    // /**
    //  * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question")
    //  */
    // private $answers;

    // public function __construct()
    // {
    //     $this->answers = new ArrayCollection();
    // }

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

    // public function getAnswers(): Collection
    // {
    //     return $this->answers;
    // }

    // public function addAnswer(Answer $answer): self
    // {
    //     $this->answers->add($answer);

    //     return $this;
    // }

    // public function removeAnswer(Answer $answer): self
    // {
    //     $this->answers->removeElement($answer);

    //     return $this;
    // }

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }
}