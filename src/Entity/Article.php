<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['article:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['article:read', 'article:write'])]
    private $sourceName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['article:read', 'article:write'])]
    private $name;

    #[ORM\Column(type: 'text')]
    #[Groups(['article:read', 'article:write'])]
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    public function setSourceName(string $sourceName): self
    {
        $this->sourceName = $sourceName;
        return $this;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
}
