  Feature: My project is accessible
    # not technic action
    In order to access to my project
    # utilisateur
    As a visitor
    # technic action
    I need to see pages via my browser

    @javascript
    Scenario: I can see home page
      Given I am on "/"
      When I wait "2" sec
      Then I should see "Ajout de contact"
