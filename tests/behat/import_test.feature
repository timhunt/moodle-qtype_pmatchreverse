@ou @ou_vle @qtype @qtype_pmatchreverse
Feature: Test importing reverse pattern-match questions
  In order to easily reuse questions
  As an teacher
  I need to be able to import reverse pattern-match questions.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And I log in as "teacher"
    And I follow "Course 1"

  @javascript
  Scenario: import a reverse pattern match question.
    # Import sample file.
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/pmatchreverse/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "1. Please enter a pattern-match expression which matches, or not, the given example sentences."
    And I press "Continue"
    And I should see "Imported pattern match question"
