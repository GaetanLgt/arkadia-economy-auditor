<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class PlayerActivity
{
    /**
     * @param array<int> $hourlyDistribution
     */
    public function __construct(
        public int $totalUniquePlayers,
        public float $avgSessionDuration,
        public int $peakHour,
        public float $retentionRate,
        public array $hourlyDistribution,
    ) {}

    public function toArray(): array
    {
        return [
            'total_unique_players' => $this->totalUniquePlayers,
            'avg_session_duration_minutes' => round($this->avgSessionDuration, 2),
            'peak_hour' => $this->peakHour,
            'retention_rate_percent' => round($this->retentionRate, 2),
            'hourly_distribution' => $this->hourlyDistribution,
        ];
    }
}
