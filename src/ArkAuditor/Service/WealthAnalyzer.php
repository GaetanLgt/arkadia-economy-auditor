<?php

declare(strict_types=1);

namespace App\ArkAuditor\Service;

use App\ArkAuditor\DTO\PlayerWealth;
use App\ArkAuditor\DTO\WealthDistribution;

final readonly class WealthAnalyzer
{
    private const RARE_ITEMS = [
        'TekRifle', 'TekSaddle', 'Element', 'BlackPearl', 'TekGenerator',
        'TekReplicator', 'TekTransmitter', 'TekTurret', 'TekForceField',
    ];
    
    private const WEIGHTS = [
        'item' => 1,
        'structure' => 10,
        'dino' => 5,
        'rare_item' => 50,
    ];

    public function analyze(array $playersData): WealthDistribution
    {
        $players = [];
        
        foreach ($playersData as $playerData) {
            $steamId = $playerData['steam_id'] ?? $playerData['id'] ?? 'unknown';
            
            $rareItems = $this->countRareItems($playerData['inventory'] ?? []);
            $totalItems = count($playerData['inventory'] ?? []);
            $structures = $playerData['structure_count'] ?? 0;
            $dinos = $playerData['dino_count'] ?? 0;
            
            $wealthScore = $this->calculateWealthScore(
                $totalItems,
                $structures,
                $dinos,
                $rareItems
            );
            
            $players[$steamId] = new PlayerWealth(
                steamId: $steamId,
                playerName: $playerData['name'] ?? 'Unknown',
                totalItems: $totalItems,
                rareItems: $rareItems,
                structures: $structures,
                dinos: $dinos,
                wealthScore: $wealthScore,
            );
        }

        $wealthScores = array_map(fn(PlayerWealth $p) => $p->wealthScore, $players);
        
        return new WealthDistribution(
            players: $players,
            giniCoefficient: $this->calculateGiniCoefficient($wealthScores),
            top10PercentWealth: $this->calculateTopPercentWealth($wealthScores, 10),
            medianWealth: $this->calculateMedian($wealthScores),
            meanWealth: count($wealthScores) > 0 ? array_sum($wealthScores) / count($wealthScores) : 0,
        );
    }

    private function countRareItems(array $inventory): int
    {
        $count = 0;
        foreach ($inventory as $item) {
            $itemType = $item['type'] ?? $item['class_name'] ?? '';
            if (in_array($itemType, self::RARE_ITEMS, true)) {
                $count++;
            }
        }
        return $count;
    }

    private function calculateWealthScore(int $items, int $structures, int $dinos, int $rareItems): int
    {
        return 
            $items * self::WEIGHTS['item'] +
            $structures * self::WEIGHTS['structure'] +
            $dinos * self::WEIGHTS['dino'] +
            $rareItems * self::WEIGHTS['rare_item'];
    }

    private function calculateGiniCoefficient(array $values): float
    {
        if (empty($values)) {
            return 0.0;
        }

        sort($values);
        $n = count($values);
        
        $cumsum = 0;
        foreach ($values as $i => $value) {
            $cumsum += ($i + 1) * $value;
        }
        
        $total = array_sum($values);
        
        return $total > 0 
            ? (2 * $cumsum) / ($n * $total) - ($n + 1) / $n 
            : 0.0;
    }

    private function calculateTopPercentWealth(array $values, int $percent): float
    {
        if (empty($values)) {
            return 0.0;
        }

        rsort($values);
        $topN = max(1, (int)ceil(count($values) * $percent / 100));
        $topSum = array_sum(array_slice($values, 0, $topN));
        $totalSum = array_sum($values);
        
        return $totalSum > 0 ? ($topSum / $totalSum) * 100 : 0.0;
    }

    private function calculateMedian(array $values): float
    {
        if (empty($values)) {
            return 0.0;
        }

        sort($values);
        $count = count($values);
        $middle = (int)floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return (float)$values[$middle];
    }
}
