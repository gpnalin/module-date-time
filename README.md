# Aligent DateTime API

## Overview

The Aligent DateTime API is a Magento 2 module that provides functionality to calculate the difference between two dates. It supports various calculation types and can be accessed via both REST API and GraphQL.

## Features

- Calculate the difference between two dates in:
    - Days
    - Weekdays
    - Weeks
    - Hours
    - Minutes
    - Seconds
    - Years
- Support for standard datetime formats with timezone
- REST API endpoint
- GraphQL query

## Installation

1. Copy the `Aligent_DateTime` module to your Magento 2 `app/code` directory.
2. Enable the module by running:
   ```
   bin/magento module:enable Aligent_DateTime
   ```
3. Run the Magento setup upgrade:
   ```
   bin/magento setup:upgrade
   ```
4. Compile Dependency Injection:
   ```
   bin/magento setup:di:compile
   ```
5. Clean the cache:
   ```
   bin/magento cache:clean
   ```

## Usage

### REST API

#### Endpoint

`POST /V1/aligent-datetime/calculate`

#### Parameters

- `startDate` (string): The start date in ISO 8601 format (e.g., "2023-01-01T00:00:00+00:00")
- `endDate` (string): The end date in ISO 8601 format (e.g., "2023-01-10T00:00:00+00:00")
- `calculationType` (string): The type of calculation to perform (days, weekdays, weeks, hours, minutes, seconds, years)

#### Example Request

```json
POST /V1/aligent-datetime/calculate
Content-Type: application/json

{
  "startDate": "2023-01-01T00:00:00+00:00",
  "endDate": "2023-01-10T00:00:00+00:00",
  "calculationType": "days"
}
```

#### Example Response

```json
3
```

### GraphQL

#### Query

```graphql
query CalculateDateTimeDifference(
  $startDate: String!,
  $endDate: String!,
  $calculationType: CalculationType!
) {
  calculateDateTimeDifference(
    startDate: $startDate,
    endDate: $endDate,
    calculationType: $calculationType
  ) {
    result
  }
}
```

#### Variables

```json
{
  "startDate": "2023-01-01T00:00:00+00:00",
  "endDate": "2023-01-10T00:00:00+00:00",
  "calculationType": "days"
}
```

#### Example Response

```json
{
  "data": {
    "DiffCalculatorQuery": {
      "result": 9
    }
  }
}
```

## Error Handling

Both the REST API and GraphQL query will return appropriate error messages if the input is invalid or if an unexpected error occurs during calculation.

## Supported Calculation Types

- `days`: Calculate the total number of days
- `weekdays`: Calculate the number of weekdays (Monday to Friday)
- `weeks`: Calculate the number of complete weeks
- `hours`: Calculate the total number of hours
- `minutes`: Calculate the total number of minutes
- `seconds`: Calculate the total number of seconds
- `years`: Calculate the number of years

## Notes

- All dates should be provided in standard datetime formats with timezone information.
- If no timezone is specified, UTC is assumed.
- The `endDate` must be greater than or equal to the `startDate`.
