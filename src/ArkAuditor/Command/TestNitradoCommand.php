<?php

declare(strict_types=1);

namespace App\ArkAuditor\Command;

use App\ArkAuditor\Client\NitradoApiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ark:test:nitrado',
    description: 'Test la connexion à l\'API Nitrado',
)]
final class TestNitradoCommand extends Command
{
    public function __construct(
        private readonly NitradoApiClient $nitradoClient,
        private readonly string $serviceId,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Test connexion Nitrado API');

        try {
            $io->section('Service ID: ' . $this->serviceId);
            
            $serviceInfo = $this->nitradoClient->getServiceInfo($this->serviceId);
            
            $io->success('✅ Connexion réussie !');
            $io->definitionList(
                ['Nom du serveur' => $serviceInfo['gameserver']['game'] ?? 'N/A'],
                ['Statut' => $serviceInfo['gameserver']['status'] ?? 'N/A'],
                ['IP' => $serviceInfo['gameserver']['ip'] ?? 'N/A'],
                ['Slots' => $serviceInfo['gameserver']['slots'] ?? 'N/A'],
            );
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('❌ Échec de connexion : ' . $e->getMessage());
            $io->note([
                'Vérifications :',
                '1. Token API valide dans .env.local',
                '2. Service ID correct',
                '3. Permissions du token (gameserver:read)',
            ]);
            return Command::FAILURE;
        }
    }
}
