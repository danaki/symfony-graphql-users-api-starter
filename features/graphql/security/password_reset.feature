Feature: As a registered User, I want to manage my account credentials.

  Background:
    Given I load following users:
      | email              | plainPassword  | validationToken                      | resetToken                           | validated  | active |
      | test@example.com   | test           | 00000000-0000-0000-0000-000000000001 | 00000000-0000-0000-0000-000000000002 | true       | true   |

  Scenario: As a normal user, I want to reset my password using blank email.
    Given I send the following GraphQL request:
    """
    mutation resetPasswordWithEmptyEmail {
        passwordReset(input: {email: ""}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "validation_email_empty"

  Scenario: As a normal user, I want to reset my password using invalid email.
    Given I send the following GraphQL request:
    """
    mutation resetPasswordWithInvalidEmail {
        passwordReset(input: {email: "invalid"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "validation_email_invalid"

  Scenario: As a normal user, I want to reset my password using wrong email.
    Given I send the following GraphQL request:
    """
    mutation resetPasswordWithWrongEmail {
        passwordReset(input: {email: "hello@gmail.com"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "user_not_found"

  Scenario: As a normal user, I want to ask for a reset token in order to reset my password with good credentials.
    Given I send the following GraphQL request:
    """
    mutation resetTokenPasswordWithGoodCredentials {
        passwordReset(input: {email: "test@example.com"}) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "data.passwordReset.result" should be equal to "true"
