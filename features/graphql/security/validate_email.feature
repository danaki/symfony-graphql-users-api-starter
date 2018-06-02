Feature: As a normal user, I want to validate my account.

  Background:
    Given I load following users:
      | email              | plainPassword  | validationToken                      | resetToken                           | validated  | active |
      | test@example.com   | test           | 00000000-0000-0000-0000-000000000001 | 00000000-0000-0000-0000-000000000002 | true       | true   |

  Scenario: As a normal user, I want to reset my password using wrong token.
    Given I send the following GraphQL request:
    """
    mutation validateEmailWithValidToken {
        validateEmail(input: {token: "00000000-0000-0000-0000-000000000001"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "data.validateEmail.result" should be equal to "true"

  Scenario: As a normal user, I want to reset my password using good credentials.
    Given I send the following GraphQL request:
    """
    mutation validateEmailWithWrongToken {
        validateEmail(input: {token: "11110000-0000-0000-0000-000000000000"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "user_not_found"