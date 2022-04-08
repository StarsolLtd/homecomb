<?php

namespace App\Util;

class Amortisation
{
    public static function calculate(
        float $totalCost,
        float $deposit,
        int $totalMonthlyPayments,
        float $annualRate
    ): array {
        $loanAmount = $totalCost - $deposit;
        $monthlyRate = ($annualRate * .01) / 12;

        $value1 = $monthlyRate * ((1 + $monthlyRate) ** $totalMonthlyPayments);
        $value2 = ((1 + $monthlyRate) ** $totalMonthlyPayments) - 1;

        $monthlyPayment = $loanAmount * ($value1 / $value2);
        $totalInterest = ($monthlyPayment * $totalMonthlyPayments) - $loanAmount;
        $totalPayable = $loanAmount + $totalInterest;

        return [
            'totalCost' => $totalCost,
            'deposit' => $deposit,
            'loanAmount' => $loanAmount,
            'totalMonthlyPayments' => $totalMonthlyPayments,
            'annualRate' => $annualRate,
            'monthlyPayment' => self::roundCurrency($monthlyPayment),
            'totalPayable' => self::roundCurrency($totalPayable),
            'totalInterest' => self::roundCurrency($totalInterest),
        ];
    }

    private static function roundCurrency(float $input): float
    {
        return round($input, 2);
    }
}
