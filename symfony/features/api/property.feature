Feature: Property
  I want to be able to interact with the Property API

  Scenario: Lookup a property ID where the property does not yet exist
    Given the request body is:
    """
    {
      "addressLine1": "249 Victoria Road",
      "postcode" : "CB4 3LF",
      "countryCode": "UK"
    }
    """
    When I request '/api/property/lookup-id' using HTTP 'POST'
    Then the response code is 200
    Then the response body contains JSON:
    """
      {
        "id": 1
      }
    """
    And a Property should exist in the database with addressLine1 of "249 Victoria Road" and postcode of "CB4 3LF"
