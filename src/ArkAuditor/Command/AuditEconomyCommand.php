<?php

declare(strict_types=1);

namespace App\ArkAuditor\Command;

use App\ArkAuditor\Entity\EconomySnapshot;
use App\ArkAuditor\Service\EconomyAuditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ark:audit:economy',
    description: 'Audite l\'économie du serveur ARK ARKADIA FRANCE',
)]
final class AuditEconomyCommand extends Command
{
    public function __construct(
        private readonly EconomyAuditor $economyAuditor,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $auditOutputPath,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('export-json', null, InputOption::VALUE_NONE, 'Exporter en JSON')
            ->addOption('save-db', null, InputOption::VALUE_NONE, 'Sauvegarder en base de données')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Chemin de sortie JSON', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('🔍 ARKADIA FRANCE - Audit Économique');
        $io->section('Configuration');
        $io->horizontalTable(
            ['Option', 'Valeur'],
            [
                ['Export JSON', $input->getOption('export-json') ? '✅' : '❌'],
                ['Sauvegarde DB', $input->getOption('save-db') ? '✅' : '❌'],
            ]
        );

        $io->section('Exécution de l\'audit...');
        
        try {
            $progressBar = $io->createProgressBar(6);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            $progressBar->setMessage('Collecte des données joueurs...');
            $progressBar->advance();

            $auditResult = $this->economyAuditor->runFullAudit();
            
            $progressBar->setMessage('Analyse terminée !');
            $progressBar->finish();
            $io->newLine(2);

            $this->displayResults($io, $auditResult);

            if ($input->getOption('export-json')) {
                $this->exportJson($io, $auditResult, $input->getOption('output'));
            }

            if ($input->getOption('save-db')) {
                $this->saveToDatabase($io, $auditResult);
            }

            $io->success('Audit terminé avec succès !');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error([
                'Erreur lors de l\'audit:',
                $e->getMessage(),
            ]);
            return Command::FAILURE;
        }
    }

    private function displayResults(SymfonyStyle $io, $result): void
    {
        $io->section('📊 Résultats de l\'audit');

        $wealth = $result->wealthDistribution;
        $io->definitionList(
            ['Joueurs totaux' => count($wealth->players)],
            ['Gini coefficient' => sprintf('%.4f', $wealth->giniCoefficient)],
            ['Top 10% détient' => sprintf('%.2f%%', $wealth->top10PercentWealth)],
            ['Richesse médiane' => sprintf('%.0f', $wealth->medianWealth)],
        );

        $dinos = $result->dinoDistribution;
        $io->definitionList(
            ['Dinos totaux' => $dinos->totalDinos],
            ['Médiane/joueur' => sprintf('%.0f', $dinos->medianPerPlayer)],
            ['Hoarders (>80)' => count($dinos->hoarders)],
        );

        $inflation = $result->inflation;
        $io->definitionList(
            ['Inflation moyenne' => sprintf('%.2f%%', $inflation->averageInflation)],
        );

        $activity = $result->playerActivity;
        $io->definitionList(
            ['Joueurs actifs (7j)' => $activity->totalUniquePlayers],
            ['Durée session moy.' => sprintf('%.0f min', $activity->avgSessionDuration)],
            ['Heure de pointe' => sprintf('%dh00', $activity->peakHour)],
        );
    }

    private function exportJson(SymfonyStyle $io, $result, ?string $customPath): void
    {
        $io->section('📄 Export JSON');

        $outputPath = $customPath ?? $this->auditOutputPath;
        $filename = sprintf(
            '%s/economy_audit_%s.json',
            rtrim($outputPath, '/'),
            $result->timestamp->format('Y-m-d_His')
        );

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        file_put_contents(
            $filename,
            json_encode(
                $result->toArray(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            )
        );

        $io->success("Exporté vers: {$filename}");
    }

    private function saveToDatabase(SymfonyStyle $io, $result): void
    {
        $io->section('💾 Sauvegarde en base de données');

        $snapshot = new EconomySnapshot();
        $snapshot->setAuditDate($result->timestamp);
        $snapshot->setServerId($result->serverId);
        $snapshot->setTotalPlayers(count($result->wealthDistribution->players));
        $snapshot->setTotalDinos($result->dinoDistribution->totalDinos);
        $snapshot->setGiniCoefficient((string) $result->wealthDistribution->giniCoefficient);
        $snapshot->setAverageInflation((string) $result->inflation->averageInflation);
        $snapshot->setRawData($result->toArray());

        $this->entityManager->persist($snapshot);
        $this->entityManager->flush();

        $io->success('Données sauvegardées en base !');
    }
}
