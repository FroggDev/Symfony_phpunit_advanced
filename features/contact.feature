Feature: Contact admin
    # not technic action
  In order to to manages contacts
  # utilisateur
  As an admin
  # technic action
  I need to able to add contact

  Scenario: Before test i recreate empty database
    Given I start the scenario
    Then I recreate database

  @javascript
  Scenario: Add a contact with success
    Given I am on "/contact/create.html"
    When I fill in "contact_firstname" with "Frogg"
    And I fill in "contact_lastname" with "Frogg"
    And I fill in "contact_phone" with "0000000"
    And I fill in "contact_email" with "test@frogg.fr"
    And I press "Edit"
    And I wait "2" sec
    Then I should see "Saved !"
    And Contact "test@frogg.fr" should be in database
