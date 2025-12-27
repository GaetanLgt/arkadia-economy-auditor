<?php

declare(strict_types=1);

namespace App\ArkAuditor\DTO;

final readonly class ResourceFlow
{
    /**
     * @param array<string, int> $inputs
     * @param array<string, int> $outputs
     * @param array<string, int> $netBalance
     */
    public function __construct(
        public array $inputs,
        public array $outputs,
        public array $netBalance,
    ) {}

    public function toArray(): array
    {
        return [
            'inputs' => $this->inputs,
            'outputs' => $this->outputs,
            'net_balance' => $this->netBalance,
        ];
    }
}
