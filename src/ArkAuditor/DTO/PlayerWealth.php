<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class PlayerWealth
{
    public function __construct(
        public string $steamId,
        public string $playerName,
        public int $totalItems,
        public int $rareItems,
        public int $structures,
        public int $dinos,
        public int $wealthScore,
    ) {}

    public function toArray(): array
    {
        return [
            'steam_id' => $this->steamId,
            'player_name' => $this->playerName,
            'total_items' => $this->totalItems,
            'rare_items' => $this->rareItems,
            'structures' => $this->structures,
            'dinos' => $this->dinos,
            'wealth_score' => $this->wealthScore,
        ];
    }
}
