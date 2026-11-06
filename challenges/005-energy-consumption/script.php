
<?php

class DailyEnergyConsumption
{
    public function __construct(
        private float $value
    ) {
        if (!is_finite($value) || $value < 0) {
            throw new InvalidArgumentException("Consumo inválido!");
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }
}

class EnergyConsumption
{
    public function __construct(
        private array $consumption,
        private float $limit
    ) {
        if (count($consumption) <= 0) {
            throw new InvalidArgumentException("Quantidade de dias inválida!");
        }

        if (!is_finite($limit) || $limit < 0) {
            throw new InvalidArgumentException("Limite inválido!");
        }
    }

    public function getLimit(): float
    {
        return $this->limit;
    }

    public function calculateTotal(): float
    {
        return array_reduce(
            $this->consumption,
            fn($total, $current) => $total + $current->getValue(),
            0
        );
    }

    public function calculateAverage(): float
    {
        return $this->calculateTotal() / count($this->consumption);
    }

    public function isAboveLimit(): bool
    {
        return $this->calculateAverage() > $this->limit;
    }
}

try {
    do {
        $days = filter_var(
            readline("Quantidade de dias: "),
            FILTER_VALIDATE_INT
        );

        if ($days === false || $days <= 0) {
            echo " Entrada inválida! Informe dias maiores que 0 e consumos não negativos.\n";
        }
    } while ($days === false || $days <= 0);

    $consumptions = [];

    for ($i = 0; $i < $days; $i++) {
        do {
            $input = readline("Consumo do dia " . ($i + 1) . " (kWh): ");
            $consumption = filter_var($input, FILTER_VALIDATE_FLOAT);

            if ($consumption === false || !is_finite($consumption) || $consumption < 0) {
                echo "Entrada inválida! Informe dias maiores que 0 e consumos não negativos.\n";
            }
        } while ($consumption === false || !is_finite($consumption) || $consumption < 0);

        $consumptions[] = new DailyEnergyConsumption($consumption);
    }

    do {
        $input = readline("Limite diário (kWh): ");
        $limit = filter_var($input, FILTER_VALIDATE_FLOAT);

        if ($limit === false || !is_finite($limit) || $limit < 0) {
            echo "Informe um limite válido e não negativo.\n";
        }
    } while ($limit === false || !is_finite($limit) || $limit < 0);

    $report = new EnergyConsumption($consumptions, $limit);

    echo "\nConsumo Total: " . $report->calculateTotal() . " kWh | ";
    echo "Média Diária: " . $report->calculateAverage() . " kWh\n";

    if ($report->isAboveLimit()) {
        echo "Atenção: consumo médio ACIMA do limite estipulado (" . $report->getLimit() . ") .\n";
    } else {
        echo "Parabéns: consumo médio DENTRO do limite.\n";
    }
} catch (InvalidArgumentException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
