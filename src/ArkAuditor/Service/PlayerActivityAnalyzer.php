<?php

declare(strict_types=1);

namespace App\ArkAuditor\Service;

use App\ArkAuditor\DTO\PlayerActivity;
use App\ArkAuditor\Client\NitradoApiClient;
use Psr\Log\LoggerInterface;

final readonly class PlayerActivityAnalyzer
{
    public function __construct(
        private NitradoApiClient $nitradoClient,
        private LoggerInterface $logger,
    ) {}

    public function analyze(string $serviceId): PlayerActivity
    {
        $sessions = $this->fetchPlayerSessions($serviceId, 7);
        
        if (empty($sessions)) {
            return new PlayerActivity(
                totalUniquePlayers: 0,
                avgSessionDuration: 0.0,
                peakHour: 0,
                retentionRate: 0.0,
                hourlyDistribution: array_fill(0, 24, 0),
            );
        }
        
        $uniquePlayers = array_unique(array_column($sessions, 'player_id'));
        $durations = array_column($sessions, 'duration');
        $avgDuration = array_sum($durations) / count($durations);
        
        $hourCounts = array_fill(0, 24, 0);
        foreach ($sessions as $session) {
            $hour = $session['start_hour'] ?? 0;
            $hourCounts[$hour]++;
        }
        
        $peakHour = array_search(max($hourCounts), $hourCounts);
        
        return new PlayerActivity(
            totalUniquePlayers: count($uniquePlayers),
            avgSessionDuration: $avgDuration,
            peakHour: $peakHour,
            retentionRate: 0.0,
            hourlyDistribution: $hourCounts,
        );
    }

    private function fetchPlayerSessions(string $serviceId, int $days): array
    {
        $this->logger->info('Fetching player sessions', ['days' => $days]);
        return [];
    }
}
