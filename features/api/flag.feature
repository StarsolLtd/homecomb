Feature: Review
  I want to be able to interact with the Flag API

  Scenario: Submit a Flag for a Review
    Given the request body is:
    """
    {
      "entityName": "Review",
      "entityId": 1,
      "content": "This is a test",
      "googleReCaptchaToken": "SAMPLE"
    }
    """
    When I request '/api/flag' using HTTP 'POST'
    Then the response code is 201
    Then the response body contains JSON:
    """
      {
        "success": true
      }
    """
    And a Flag should exist in the database with an entityName of "Review", an entityId of 1, and content of "This is a test"
