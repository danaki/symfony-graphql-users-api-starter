Feature: As a normal user, I want to log in myself.

  Background:
    Given I load following users:
      | email                    | plainPassword | validationToken                      | resetToken                           | validated | active |
      | test@example.com         | test          | 00000000-0000-0000-0000-000000000001 | 00000000-0000-0000-0000-000000000001 | true      | true   |
      | disabled@example.com     | test          | 00000000-0000-0000-0000-000000000002 | 00000000-0000-0000-0000-000000000002 | true      | false  |
      | notvalidated@example.com | test          | 00000000-0000-0000-0000-000000000003 | 00000000-0000-0000-0000-000000000003 | false     | true   |

  Scenario: I need to see if I can login with wrong credentials.
    When I send the following GraphQL request:
    """
    mutation LoginWithWrongCredentials {
        login (input: {email: "test@example.com", password: "wrong_password"}) {
            token
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "login_failed"

  Scenario: I need to see if I can login with a wrong account.
    When I send the following GraphQL request:
    """
    mutation LoginWithWrongAccount {
        login (input: {email: "tutut@gmail.com", password: "tutu"}) {
            token
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "login_failed"

  Scenario: I need to see if I can login with good credentials
    When I send the following GraphQL request:
    """
    mutation LoginWithGoodCredentials {
        login(input: {email: "test@example.com", password: "test"}) {
            token
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "data.login.token" should not be null

  Scenario: I need to see if I can login with disabled account.
    When I send the following GraphQL request:
    """
    mutation LoginWithDisabledAccount {
        login (input: {email: "disabled@example.com", password: "test"}) {
            token
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "user_disabled"

  Scenario: I need to see if I can login with not validated account.
    When I send the following GraphQL request:
    """
    mutation LoginWithWrongCredentials {
        login (input: {email: "notvalidated@example.com", password: "test"}) {
            token
        }
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "errors[0].message" should be equal to "user_not_validated"