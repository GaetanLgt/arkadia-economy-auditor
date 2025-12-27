<?php

declare(strict_types=1);

namespace App\ArkAuditor\Service;

use App\ArkAuditor\DTO\InflationData;
use App\ArkAuditor\DTO\ResourceInflation;
use Psr\Log\LoggerInterface;

final readonly class InflationCalculator
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function calculate(string $serviceId): InflationData
    {
        $current = $this->getResourceAvailability($serviceId, 'now');
        $previous = $this->getResourceAvailability($serviceId, '-30 days');
        
        $inflationRates = [];
        
        foreach ($current as $resource => $currAvail) {
            $prevAvail = $previous[$resource] ?? 0;
            if ($prevAvail > 0) {
                $change = (($currAvail - $prevAvail) / $prevAvail) * 100;
                $inflationRates[$resource] = new ResourceInflation(
                    resource: $resource,
                    currentAvailability: $currAvail,
                    previousAvailability: $prevAvail,
                    changePercent: $change,
                );
            }
        }
        
        $avgInflation = !empty($inflationRates)
            ? array_sum(array_map(fn($r) => $r->changePercent, $inflationRates)) / count($inflationRates)
            : 0.0;
        
        return new InflationData(
            byResource: $inflationRates,
            averageInflation: $avgInflation,
        );
    }

    private function getResourceAvailability(string $serviceId, string $period): array
    {
        $this->logger->info('Getting resource availability', ['period' => $period]);
        return [];
    }
}
