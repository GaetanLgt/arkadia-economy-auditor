<?php

declare(strict_types=1);

namespace App\ArkAuditor\Service;

use App\ArkAuditor\Client\NitradoApiClient;
use App\ArkAuditor\DTO\AuditResult;
use App\ArkAuditor\DTO\ResourceFlow;
use Psr\Log\LoggerInterface;

final readonly class EconomyAuditor
{
    public function __construct(
        private NitradoApiClient $nitradoClient,
        private WealthAnalyzer $wealthAnalyzer,
        private DinoAnalyzer $dinoAnalyzer,
        private InflationCalculator $inflationCalculator,
        private PlayerActivityAnalyzer $activityAnalyzer,
        private LoggerInterface $logger,
        private string $serviceId,
    ) {}

    public function runFullAudit(): AuditResult
    {
        $this->logger->info('🔍 Starting full economy audit', [
            'service_id' => $this->serviceId,
        ]);

        $timestamp = new \DateTimeImmutable();

        $this->logger->info('📊 Fetching player data...');
        $playersData = $this->nitradoClient->getPlayers($this->serviceId);

        $this->logger->info('💰 Analyzing wealth distribution...');
        $wealthDistribution = $this->wealthAnalyzer->analyze($playersData);

        $this->logger->info('🦖 Analyzing dino distribution...');
        $dinoDistribution = $this->dinoAnalyzer->analyze($this->serviceId);

        $this->logger->info('📈 Calculating inflation...');
        $inflation = $this->inflationCalculator->calculate($this->serviceId);

        $this->logger->info('👥 Analyzing player activity...');
        $playerActivity = $this->activityAnalyzer->analyze($this->serviceId);

        $this->logger->info('📦 Analyzing resource flow...');
        $resourceFlow = $this->analyzeResourceFlow();

        $this->logger->info('✅ Audit completed successfully');

        return new AuditResult(
            timestamp: $timestamp,
            serverId: $this->serviceId,
            wealthDistribution: $wealthDistribution,
            dinoDistribution: $dinoDistribution,
            inflation: $inflation,
            playerActivity: $playerActivity,
            resourceFlow: $resourceFlow,
        );
    }

    private function analyzeResourceFlow(): ResourceFlow
    {
        return new ResourceFlow(
            inputs: [],
            outputs: [],
            netBalance: [],
        );
    }
}
