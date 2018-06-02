Feature: As a normal user, I want to register myself.

  Background:
    Given I load following users:
      | email              | plainPassword  | validationToken                      | resetToken                           | validated  | active |
      | test@example.com   | test           | 00000000-0000-0000-0000-000000000001 | 00000000-0000-0000-0000-000000000002 | true       | true   |

  Scenario: I need to see if I can create an account with invalid email
    Given I send the following GraphQL request:
    """
    mutation RegisterWithInvalidEmail {
        signup(input: { email: "blabla", password: "test" }) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "validation_email_invalid"

  Scenario: I need to see if I can create an account with missing email
    Given I send the following GraphQL request:
    """
    mutation RegisterWithMissingEmail {
        signup(input: { email: "", password: "test" }) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "validation_email_empty"

  Scenario: I need to see if I can register an user with an existing email
    Given I send the following GraphQL request:
    """
    mutation RegisterWithAnExistingEmail {
        signup(input: { email: "test@example.com", password: "password" }) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "validation_email_not_unique"

  Scenario: I need to see if I can create an account with all the parameters.
    Given I send the following GraphQL request:
    """
    mutation RegisterWithAllParameters {
        signup(input: { email: "yuri@example.com", password: "yuri" }) {
            result
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "data.signup.result" should be equal to "true"
