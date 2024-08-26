# Aligent DateTime API

## Overview

The Aligent DateTime API is a Magento 2 module that provides functionality to calculate the difference between two dates. It supports various calculation types and can be accessed via both REST API and GraphQL.

## Features

- Calculate the difference between two dates in:
  - `days`: Calculate the total number of days
  - `weekdays`: Calculate the number of weekdays (Monday to Friday)
  - `weeks`: Calculate the number of complete weeks
  - `hours`: Calculate the total number of hours
  - `minutes`: Calculate the total number of minutes
  - `seconds`: Calculate the total number of seconds
  - `years`: Calculate the number of years
- Support for standard datetime formats with timezone
- REST API endpoint
- GraphQL query
- Unit test and API/GraphQL functional testing

## Installation

1. If you already have Magento instance setup, skip to #6.
2. Create your project directory then go into it:
   ```
   mkdir magento.test; cd $_;
   ```
3. Download the Docker Compose template:
   ```
   curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/template | bash
   ```
4. Download the version of Magento you want to use with:
   ```
   bin/download 2.4.7 community
   ```
5. Run the setup installer for Magento:
   ```
   bin/setup magento.test
   ```
6. Install the module:
   ```
   bin/composer require gpnalin/module-date-time
   
   # or clone the module to app/code/Aligent/DateTime
   
   mkdir -p app/code/Aligent;
   cd $_;
   git clone git@github.com:gpnalin/module-date-time.git DateTime;
   ```
7. Enable the module by running:
   ```
   bin/magento module:enable Aligent_DateTime
   ```
8. Run the Magento setup upgrade:
   ```
   bin/magento setup:upgrade
   ```
9. Compile Dependency Injection:
   ```
   bin/magento setup:di:compile
   ```
10. Clean the cache:
   ```
   bin/magento cache:clean
   ```

## Usage

### REST API

#### Endpoint

`POST /V1/datetime/calculate`

#### Parameters

- `startDate` (string): The start date in ISO 8601 format (e.g., "2023-01-01T00:00:00+00:00")
- `endDate` (string): The end date in ISO 8601 format (e.g., "2023-01-10T00:00:00+00:00")
- `calculationType` (string): The type of calculation to perform (days, weekdays, weeks, hours, minutes, seconds, years)

#### Example Request

```json
POST /V1/datetime/calculate
Content-Type: application/json

{
  "startDate": "2023-01-01T00:00:00+00:00",
  "endDate": "2023-01-10T00:00:00+00:00",
  "calculationType": "days"
}
```

#### Example Response

```json
{
    "result": 9
}
```

### GraphQL

#### Query

```graphql
query DiffCalculatorQuery(
  $startDate: String!,
  $endDate: String!,
  $calculationType: CalculationType!
) {
    DiffCalculatorQuery(
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


## Notes

- All dates should be provided in standard datetime formats with timezone information.
- If no timezone is specified, UTC is assumed.
- The `endDate` must be greater than or equal to the `startDate`.

## Test Coverage

- Unit Test
  ```
  /usr/local/bin/php -dmemory_limit=-1 $(pwd)/vendor/bin/phpunit --bootstrap $(pwd)/dev/tests/unit/framework/bootstrap.php --configuration $(pwd)/dev/tests/unit/phpunit.xml.dist $(pwd)/vendor/gpnalin/module-date-time/Test/Unit/
  ```
- Web API Functional Test
  ```
  /usr/local/bin/php -dmemory_limit=-1 $(pwd)/vendor/bin/phpunit -c $(pwd)/dev/tests/api-functional/phpunit_rest.xml.dist $(pwd)/vendor/gpnalin/module-date-time/Test/Api/
  ```
- GraphQL Functional Test
  ```
  /usr/local/bin/php -dmemory_limit=-1 $(pwd)/vendor/bin/phpunit -c $(pwd)/dev/tests/api-functional/phpunit_graphql.xml.dist $(pwd)/vendor/gpnalin/module-date-time/Test/GraphQl/
  ```
