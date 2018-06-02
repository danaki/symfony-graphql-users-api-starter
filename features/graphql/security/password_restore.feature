Feature: As a registered User, I want to manage my account credentials.

  Background:
    Given I load following users:
      | email              | plainPassword  | validationToken                      | resetToken                           | validated  | active |
      | test@example.com   | test           | 00000000-0000-0000-0000-000000000001 | 00000000-0000-0000-0000-000000000002 | true       | true   |

  Scenario: As a normal user, I want to reset my password using wrong token.
    Given I send the following GraphQL request:
    """
    mutation resetPasswordWithWrongToken {
        passwordRestore(input: {token: "00000000-0000-0000-0000-000000000000", password: "password"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "user_not_found"

  Scenario: As a normal user, I want to reset my password using good credentials.
    Given I send the following GraphQL request:
    """
    mutation resetPasswordWithWrongEmail {
        passwordRestore(input: {token: "00000000-0000-0000-0000-000000000002", password: "password"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "data.passwordRestore.result" should be equal to "true"