<?php
namespace Aligent\DateTime\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

class AligentDateTimeWebApiTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/datetime/calculate';

    /**
     * @dataProvider calculateDataProvider
     */
    public function testCalculate($startDate, $endDate, $calculationType, $expectedResult)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calculationType' => $calculationType
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('result', $response);
        $this->assertEquals($expectedResult, $response['result']);
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
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calculationType' => $calculationType
        ];

        $this->expectExceptionMessage($expectedErrorMessage);
        $this->_webApiCall($serviceInfo, $requestData);
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
     */
    public function testCalculateWithInvalidEndDate($startDate, $endDate, $calculationType, $expectedErrorMessage)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calculationType' => $calculationType
        ];

        $this->expectExceptionMessage($expectedErrorMessage);
        $this->_webApiCall($serviceInfo, $requestData);
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
     * @dataProvider validDateFormatDataProvider
     */
    public function testCalculateWithVariousDateFormats($startDate, $endDate)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calculationType' => 'days'
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('result', $response);
        $this->assertIsNumeric($response['result']);
    }

    public function validDateFormatDataProvider(): array
    {
        return [
            ['2022-06-02T16:58:35+00:00', '2022-06-03T16:58:35+00:00'],
            ['Thursday, 02-Jun-2022 16:58:35 UTC', 'Friday, 03-Jun-2022 16:58:35 UTC'],
            ['2022-06-02T16:58:35+0000', '2022-06-03T16:58:35+0000'],
            ['Thu, 02 Jun 22 16:58:35 +0000', 'Fri, 03 Jun 22 16:58:35 +0000'],
            ['2022-06-02', '2022-06-03'],
            ['2022-06-02 16:58:35', '2022-06-03 16:58:35'],
            ['2022-06-02 16:58:35 Asia/Dubai', '2022-06-03 16:58:35 Asia/Dubai'],
        ];
    }

    public function testCalculateWithSameStartAndEndDate()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => '2024-01-01T00:00:00+00:00',
            'endDate' => '2024-01-01T00:00:00+00:00',
            'calculationType' => 'days'
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('result', $response);
        $this->assertEquals(0, $response['result']);
    }

    public function testCalculateWithLeapYear()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => '2024-02-28T00:00:00+00:00',
            'endDate' => '2024-03-01T00:00:00+00:00',
            'calculationType' => 'days'
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('result', $response);
        $this->assertEquals(2, $response['result']);
    }

    public function testCalculateWithDifferentTimezones()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
        ];

        $requestData = [
            'startDate' => '2024-01-01T00:00:00+00:00',
            'endDate' => '2024-01-02T02:00:00+02:00',
            'calculationType' => 'hours'
        ];

        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertArrayHasKey('result', $response);
        $this->assertEquals(24, $response['result']);
    }
}
