<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class DinoDistribution
{
    /**
     * @param array<string, int> $byPlayer
     * @param array<string, int> $bySpecies
     * @param array<int> $levelDistribution
     * @param array<string, int> $hoarders
     */
    public function __construct(
        public int $totalDinos,
        public array $byPlayer,
        public array $bySpecies,
        public array $levelDistribution,
        public float $medianPerPlayer,
        public array $hoarders,
    ) {}

    public function toArray(): array
    {
        $byPlayer = $this->byPlayer;
        $bySpecies = $this->bySpecies;
        arsort($byPlayer);
        arsort($bySpecies);
        
        return [
            'total_dinos' => $this->totalDinos,
            'by_player' => $byPlayer,
            'by_species' => $bySpecies,
            'level_distribution' => $this->levelDistribution,
            'statistics' => [
                'median_dinos_per_player' => round($this->medianPerPlayer, 2),
                'hoarders' => $this->hoarders,
                'top_10_owners' => array_slice($byPlayer, 0, 10, true),
                'most_popular_species' => array_slice($bySpecies, 0, 10, true),
            ],
        ];
    }
}
