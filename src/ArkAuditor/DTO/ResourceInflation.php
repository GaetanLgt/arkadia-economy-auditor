<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class ResourceInflation
{
    public function __construct(
        public string $resource,
        public int $currentAvailability,
        public int $previousAvailability,
        public float $changePercent,
    ) {}

    public function toArray(): array
    {
        return [
            'current_availability' => $this->currentAvailability,
            'previous_availability' => $this->previousAvailability,
            'change_percent' => round($this->changePercent, 2),
        ];
    }
}
