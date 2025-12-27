<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class InflationData
{
    /**
     * @param array<string, ResourceInflation> $byResource
     */
    public function __construct(
        public array $byResource,
        public float $averageInflation,
    ) {}

    public function toArray(): array
    {
        return [
            'by_resource' => array_map(
                fn(ResourceInflation $ri) => $ri->toArray(),
                $this->byResource
            ),
            'average_inflation' => round($this->averageInflation, 2),
        ];
    }
}
