@ou @ou_vle @qtype @qtype_pmatchreverse
Feature: Import and export reverse pattern-match questions
  As a teacher
  In order to reuse my reverse pattern-match questions
  I need to be able to import and export them

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
    And I am on "Course 1" course homepage

  @javascript @_file_upload
  Scenario: Import and export reverse pattern-match questions
    # Import sample file.
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/pmatchreverse/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "1. Please enter a pattern-match expression which matches, or not, the given example sentences."
    And I press "Continue"
    And I should see "Imported pattern match question"

    # Now export again.
    And I am on "Course 1" course homepage
    When I navigate to "Question bank" in current page administration
    And I select "Export" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I press "Export questions to file"
    Then following "click here" should download between "1000" and "2000" bytes
    # If the download step is the last in the scenario then we can sometimes run
    # into the situation where the download page causes a http redirect but behat
    # has already conducted its reset (generating an error). By putting a logout
    # step we avoid behat doing the reset until we are off that page.
    And I log out
