Feature: Review
  I want to be able to interact with the Review API

  Scenario: Submit a review for a property
    Given the request body is:
    """
    {
      "propertyId": 1,
      "reviewerName": "Rishi Sunak",
      "reviewerEmail": "chancellor@starsol.co.uk",
      "agencyName": "Downing Street Lets",
      "agencyBranch": "Whitehall",
      "reviewTitle": "Test review title",
      "reviewContent": "Test review content",
      "overallStars": 4,
      "agencyStars": 5,
      "landlordStars": null,
      "propertyStars": 2,
      "googleReCaptchaToken": "SAMPLE"
    }
    """
    When I request '/api/submit-review' using HTTP 'POST'
    Then the response code is 201
    Then the response body contains JSON:
    """
      {
        "success": true
      }
    """
    And a Review should exist in the database with propertyId of 1 and title of "Test review title"
