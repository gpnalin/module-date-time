<?php

namespace Aligent\DateTime\Test\GraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

class AligentDateTimeGraphQlTest extends GraphQlAbstract
{
    /**
     * @dataProvider calculateDataProvider
     */
    public function testCalculate($startDate, $endDate, $calculationType, $expectedResult)
    {
        $query = $this->getQuery($startDate, $endDate, $calculationType);
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey('DiffCalculatorQuery', $response);
        $this->assertArrayHasKey('result', $response['DiffCalculatorQuery']);
        $this->assertEquals($expectedResult, $response['DiffCalculatorQuery']['result']);
    }

    public function calculateDataProvider(): array
    {
        return [
            ['2024-01-01T00:00:00+00:00', '2024-01-03T00:00:00+00:00', 'days', 2],
            ['2024-01-01T00:00:00+00:00', '2024-01-10T00:00:00+00:00', 'weekdays', 7],
            ['2024-01-01T00:00:00+00:00', '2024-01-15T00:00:00+00:00', 'weeks', 2],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T12:00:00+00:00', 'hours', 12],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T12:00:00+00:00', 'minutes', 720],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:10+00:00', 'seconds', 10],
            ['2024-01-01T00:00:00+00:00', '2025-02-01T00:00:00+00:00', 'years', 1],
            ['2024-01-05T00:00:00+00:00', '2024-01-08T00:00:00+00:00', 'weekdays', 1],
            ['2024-01-06T00:00:00+00:00', '2024-01-07T00:00:00+00:00', 'weekdays', 0],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'weekdays', 0],
        ];
    }

    /**
     * @dataProvider invalidInputDataProvider
     */
    public function testCalculateWithInvalidInputs($startDate, $endDate, $calculationType, $expectedErrorMessage)
    {
        $query = $this->getQuery($startDate, $endDate, $calculationType);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $this->graphQlQuery($query);
    }

    public function invalidInputDataProvider(): array
    {
        return [
            ['invalid-date', '2024-01-01T00:00:00+00:00', 'days', 'Invalid start date format'],
            ['2024-01-01T00:00:00+00:00', 'invalid-date', 'days', 'Invalid end date format']
        ];
    }

    /**
     * @dataProvider invalidEndDateDataProvider
     */
    public function testCalculateWithInvalidEndDate($startDate, $endDate, $calculationType, $expectedErrorMessage)
    {
        $query = $this->getQuery($startDate, $endDate, $calculationType);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $this->graphQlQuery($query);
    }

    public function invalidEndDateDataProvider(): array
    {
        return [
            ['2024-01-02T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'days', 'End date must be greater than or equal to start date'],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+02:00', 'weekdays', 'End date must be greater than or equal to start date'],
            ['2024-01-02T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'weeks', 'End date must be greater than or equal to start date'],
        ];
    }

    /**
     * @dataProvider validDateFormatDataProvider
     */
    public function testCalculateWithVariousDateFormats($startDate, $endDate)
    {
        $query = $this->getQuery($startDate, $endDate, 'days');
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey('DiffCalculatorQuery', $response);
        $this->assertArrayHasKey('result', $response['DiffCalculatorQuery']);
        $this->assertIsNumeric($response['DiffCalculatorQuery']['result']);
    }

    public function validDateFormatDataProvider(): array
    {
        return [
            ['2022-06-02T16:58:35+00:00', '2022-06-03T16:58:35+00:00'],
            ['2022-06-02T16:58:35+0000', '2022-06-03T16:58:35+0000'],
            ['2022-06-02', '2022-06-03'],
            ['2022-06-02 16:58:35', '2022-06-03 16:58:35'],
        ];
    }

    public function testCalculateWithSameStartAndEndDate()
    {
        $query = $this->getQuery('2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'days');
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey('DiffCalculatorQuery', $response);
        $this->assertArrayHasKey('result', $response['DiffCalculatorQuery']);
        $this->assertEquals(0, $response['DiffCalculatorQuery']['result']);
    }

    public function testCalculateWithLeapYear()
    {
        $query = $this->getQuery('2024-02-28T00:00:00+00:00', '2024-03-01T00:00:00+00:00', 'days');
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey('DiffCalculatorQuery', $response);
        $this->assertArrayHasKey('result', $response['DiffCalculatorQuery']);
        $this->assertEquals(2, $response['DiffCalculatorQuery']['result']);
    }

    public function testCalculateWithDifferentTimezones()
    {
        $query = $this->getQuery('2024-01-01T00:00:00+00:00', '2024-01-02T02:00:00+02:00', 'hours');
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey('DiffCalculatorQuery', $response);
        $this->assertArrayHasKey('result', $response['DiffCalculatorQuery']);
        $this->assertEquals(24, $response['DiffCalculatorQuery']['result']);
    }

    private function getQuery($startDate, $endDate, $calculationType): string
    {
        return <<<QUERY
query {
  DiffCalculatorQuery(
    startDate: "{$startDate}"
    endDate: "{$endDate}"
    calculationType: {$calculationType}
  ) {
    result
  }
}
QUERY;
    }
}
