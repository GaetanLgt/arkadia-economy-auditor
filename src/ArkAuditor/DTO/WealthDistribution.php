<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class WealthDistribution
{
    /**
     * @param array<string, PlayerWealth> $players
     */
    public function __construct(
        public array $players,
        public float $giniCoefficient,
        public float $top10PercentWealth,
        public float $medianWealth,
        public float $meanWealth,
    ) {}

    public function toArray(): array
    {
        return [
            'players' => array_map(
                fn(PlayerWealth $pw) => $pw->toArray(),
                $this->players
            ),
            'statistics' => [
                'gini_coefficient' => round($this->giniCoefficient, 4),
                'top_10_percent_wealth' => round($this->top10PercentWealth, 2),
                'median_wealth' => round($this->medianWealth, 2),
                'mean_wealth' => round($this->meanWealth, 2),
                'total_players' => count($this->players),
            ],
        ];
    }
}
