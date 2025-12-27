<?php

declare(strict_types=1);

namespace App\ArkAuditor\Entity;

use App\ArkAuditor\Repository\EconomySnapshotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EconomySnapshotRepository::class)]
#[ORM\Table(name: 'economy_snapshots')]
#[ORM\Index(columns: ['audit_date'], name: 'idx_audit_date')]
class EconomySnapshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $auditDate = null;

    #[ORM\Column(length: 255)]
    private ?string $serverId = null;

    #[ORM\Column]
    private ?int $totalPlayers = null;

    #[ORM\Column]
    private ?int $totalDinos = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $giniCoefficient = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $averageInflation = null;

    #[ORM\Column(type: Types::JSON)]
    private array $rawData = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuditDate(): ?\DateTimeImmutable
    {
        return $this->auditDate;
    }

    public function setAuditDate(\DateTimeImmutable $auditDate): static
    {
        $this->auditDate = $auditDate;
        return $this;
    }

    public function getServerId(): ?string
    {
        return $this->serverId;
    }

    public function setServerId(string $serverId): static
    {
        $this->serverId = $serverId;
        return $this;
    }

    public function getTotalPlayers(): ?int
    {
        return $this->totalPlayers;
    }

    public function setTotalPlayers(int $totalPlayers): static
    {
        $this->totalPlayers = $totalPlayers;
        return $this;
    }

    public function getTotalDinos(): ?int
    {
        return $this->totalDinos;
    }

    public function setTotalDinos(int $totalDinos): static
    {
        $this->totalDinos = $totalDinos;
        return $this;
    }

    public function getGiniCoefficient(): ?string
    {
        return $this->giniCoefficient;
    }

    public function setGiniCoefficient(string $giniCoefficient): static
    {
        $this->giniCoefficient = $giniCoefficient;
        return $this;
    }

    public function getAverageInflation(): ?string
    {
        return $this->averageInflation;
    }

    public function setAverageInflation(string $averageInflation): static
    {
        $this->averageInflation = $averageInflation;
        return $this;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function setRawData(array $rawData): static
    {
        $this->rawData = $rawData;
        return $this;
    }
}
