<?php

namespace App\Tests\Unit\Util;

use App\Util\Amortisation;
use PHPUnit\Framework\TestCase;

final class AmortisationTest extends TestCase
{
    /**
     * @dataProvider provideTestCalculate
     */
    public function testCalculate(
        array $expectedResult,
        float $totalCost,
        float $deposit,
        int $totalMonthlyPayments,
        float $annualRate,
    ): void {
        $result = Amortisation::calculate($totalCost, $deposit, $totalMonthlyPayments, $annualRate);

        $this->assertEquals($expectedResult['loanAmount'], $result['loanAmount']);
        $this->assertEquals($expectedResult['monthlyPayment'], $result['monthlyPayment']);
        $this->assertEquals($expectedResult['totalPayable'], $result['totalPayable']);
        $this->assertEquals($expectedResult['totalInterest'], $result['totalInterest']);
    }

    /**
     * @return array[]
     */
    public function provideTestCalculate(): array
    {
        return [
            [
                ['loanAmount' => 1000.00, 'monthlyPayment' => 46.14, 'totalPayable' => 1107.48, 'totalInterest' => 107.48],
                1100.00,
                100.00,
                24,
                10.0,
            ],
            [
                ['loanAmount' => 2500.00, 'monthlyPayment' => 93.14, 'totalPayable' => 2794.19, 'totalInterest' => 294.19],
                3000.00,
                500.00,
                30,
                8.8,
            ],
            [
                ['loanAmount' => 2567.89, 'monthlyPayment' => 254.85, 'totalPayable' => 2803.39, 'totalInterest' => 235.50],
                2972.40,
                404.51,
                11,
                17.9,
            ],
        ];
    }
}
