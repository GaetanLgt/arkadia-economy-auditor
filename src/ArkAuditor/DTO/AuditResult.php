<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class AuditResult
{
    public function __construct(
        public \DateTimeImmutable $timestamp,
        public string $serverId,
        public WealthDistribution $wealthDistribution,
        public DinoDistribution $dinoDistribution,
        public InflationData $inflation,
        public PlayerActivity $playerActivity,
        public ResourceFlow $resourceFlow,
    ) {}

    public function toArray(): array
    {
        return [
            'meta' => [
                'version' => '1.0.0',
                'timestamp' => $this->timestamp->format(\DateTimeInterface::ISO8601),
                'server_id' => $this->serverId,
            ],
            'wealth_distribution' => $this->wealthDistribution->toArray(),
            'dino_distribution' => $this->dinoDistribution->toArray(),
            'inflation' => $this->inflation->toArray(),
            'player_activity' => $this->playerActivity->toArray(),
            'resource_flow' => $this->resourceFlow->toArray(),
        ];
    }
}
