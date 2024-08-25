<?php

declare(strict_types=1);

namespace Aligent\DateTime\Test\Unit\Model;

use Aligent\DateTime\Api\Data\ResultInterfaceFactory;
use Aligent\DateTime\Model\DiffCalculator;
use Aligent\DateTime\Model\Result;
use DateTime;
use Exception;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Validation\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DiffCalculatorTest extends TestCase
{
    /**
     * @var DateTimeFactory|MockObject
     */
    private DateTimeFactory|MockObject $dateTimeFactoryMock;

    /**
     * @var ResultInterfaceFactory|(ResultInterfaceFactory&MockObject)|MockObject
     */
    private ResultInterfaceFactory $resultFactoryMock;

    /**
     * @var Result
     */
    private Result $resultMock;

    /**
     * @var DiffCalculator
     */
    private DiffCalculator $diffCalculator;

    protected function setUp(): void
    {
        $this->dateTimeFactoryMock = $this->getMockBuilder(DateTimeFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->addMethods(['diff'])
            ->getMock();

        $this->resultFactoryMock = $this->getMockBuilder(ResultInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->resultMock = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getResult','setResult','getData'])
            ->getMock();

        $this->diffCalculator = new DiffCalculator($this->dateTimeFactoryMock, $this->resultFactoryMock);
    }

    /**
     * @dataProvider calculateDataProvider
     * @throws Exception|ValidationException
     */
    public function testCalculate($startDate, $endDate, $calculationType, $expectedResult)
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $start->diff($end);

        // Ensuring the mock returns valid DateTime objects
        $this->dateTimeFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($start, $end);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultMock);

        $this->resultMock->expects($this->once())->method('setResult')->with($expectedResult);

        $result = $this->diffCalculator->calculate($startDate, $endDate, $calculationType);

        $this->assertEquals($this->resultMock, $result);
    }

    public function calculateDataProvider(): array
    {
        return [
            // Regular cases
            ['2024-01-01T00:00:00+00:00', '2024-01-03T00:00:00+00:00', 'days', 2],
            ['2024-01-01T00:00:00+00:00', '2024-01-10T00:00:00+00:00', 'weekdays', 7],
            ['2024-01-01T00:00:00+00:00', '2024-01-15T00:00:00+00:00', 'weeks', 2],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T12:00:00+00:00', 'hours', 12],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T12:00:00+00:00', 'minutes', 720],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:10+00:00', 'seconds', 10],
            ['2024-01-01T00:00:00+00:00', '2025-02-01T00:00:00+00:00', 'years', 1],

            // Edge cases for weekdays
            ['2024-01-05T00:00:00+00:00', '2024-01-08T00:00:00+00:00', 'weekdays', 1], // Friday to Monday
            ['2024-01-06T00:00:00+00:00', '2024-01-07T00:00:00+00:00', 'weekdays', 0], // Weekend only
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'weekdays', 0], // Same day, no weekdays
        ];
    }

    /**
     * @dataProvider invalidInputDataProvider
     */
    public function testCalculateWithInvalidInputs($startDate, $endDate, $calculationType, $expectedExceptionMessage)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->diffCalculator->calculate($startDate, $endDate, $calculationType);
    }

    public function invalidInputDataProvider(): array
    {
        return [
            ['invalid-date', '2024-01-01T00:00:00+00:00', 'days', 'Invalid start date format.'],
            ['2024-01-01T00:00:00+00:00', 'invalid-date', 'days', 'Invalid end date format.'],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'invalid-type', 'Invalid calculation type.'],
        ];
    }

    /**
     * @dataProvider invalidEndDateDataProvider
     * @throws Exception|ValidationException
     */
    public function testCalculateWithInvalidEndDate($startDate, $endDate, $calculationType, $expectedExceptionMessage)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $start->diff($end);

        // Ensuring the mock returns valid DateTime objects
        $this->dateTimeFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($start, $end);

        $this->diffCalculator->calculate($startDate, $endDate, $calculationType);
    }

    public function invalidEndDateDataProvider(): array
    {
        return [
            ['2024-01-02T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'days', 'End date must be greater than or equal to start date.'],
            ['2024-01-01T00:00:00+00:00', '2024-01-01T00:00:00+02:00', 'weekdays', 'End date must be greater than or equal to start date.'],
            ['2024-01-02T00:00:00+00:00', '2024-01-01T00:00:00+00:00', 'weeks', 'End date must be greater than or equal to start date.'],
        ];
    }

    /**
     * @dataProvider validDateDataProvider
     */
    public function testisValidDateTimeInputs($date)
    {
        $result = $this->diffCalculator->isValidDateTime($date);
        $this->assertTrue($result);
    }

    public function validDateDataProvider(): array
    {
        return [
            ['2022-06-02T16:58:35+00:00'],
            ['Thursday, 02-Jun-2022 16:58:35 UTC'],
            ['2022-06-02T16:58:35+0000'],
            ['Thu, 02 Jun 22 16:58:35 +0000'],
            ['Thursday, 02-Jun-22 16:58:35 UTC'],
            ['Thu, 02 Jun 22 16:58:35 +0000'],
            ['Thu, 02 Jun 2022 16:58:35 +0000'],
            ['Thu, 02 Jun 2022 16:58:35 +0000'],
            ['2022-06-02T16:58:35+00:00'],
            ['2022-06-02T16:58:35.698+00:00'],
            ['Thu, 02 Jun 2022 16:58:35 GMT'],
            ['Thu, 02 Jun 2022 16:58:35 +0000'],
            ['2022-06-02T16:58:35+00:00'],
            ['2022-06-02'],
            ['2022-06-02 16:58:35'],
            ['2022-06-02 16:58:35 Asia/Dubai'],
            ['2022-06-02 16:58:35']
        ];
    }
}
