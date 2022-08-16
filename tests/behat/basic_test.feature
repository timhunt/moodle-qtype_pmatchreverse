@ou @ou_vle @qtype @qtype_pmatchreverse @_switch_window @javascript
Feature: Test all the basic functionality of pmatchreverse question type
  In order train people about the pmatch syntax
  As a trainer
  I need to create and preview reverse pattern match questions.

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

  Scenario: Create, edit then preview a reverse pattern match question.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    # Create a new question.
    And I add a "Reverse pattern match" question filling the form with:
      | Question name                 | My first reverse pattern match question |
      | id_answer_0                   | The cat sat on the mat                  |
      | id_answer_1                   | The frog sat on the lilly pad           |
      | id_answer_2                   | The dog barked                          |
      | id_fraction_0                 | Should match                            |
      | id_fraction_1                 | Should match                            |
      | id_fraction_2                 | Should not match                        |
      | Hint 1                        | Please try again.                       |
      | Hint 2                        | Don't try too hard with this one.       |
    Then I should see "My first reverse pattern match question"

    # Preview it. Test correct and incorrect answers.
    And I am on the "My first reverse pattern match question" "core_question > preview" page
    And I set the following fields to these values:
      | How questions behave | Interactive with multiple tries |
      | Marked out of        | 3                               |
      | Marks                | Show mark and max               |
    And I press "Start again with these options"
    Then I should see "Please enter a pattern-match expression which matches, or not, the given example sentences."
    And the state of "Please enter a pattern-match expression" question is shown as "Tries remaining: 3"
    When I set the field "Answer" to "match(The cat sat on the mat)"
    And I press "Check"
    Then I should see "Your answer is partially correct."
    And I should see "Please try again."
    And I should see "Yes" in the "The cat sat on the mat" "table_row"
    And I should see "No" in the "The frog sat on the lilly pad" "table_row"
    And I should see "No" in the "The dog barked" "table_row"
    When I press "Try again"
    Then the state of "Please enter a pattern-match expression" question is shown as "Tries remaining: 2"
    When I set the field "Answer" to "match_w(sat)"
    And I press "Check"
    Then I should see "Your answer is correct."
    And the state of "Please enter a pattern-match expression" question is shown as "Correct"

    # Backup the course and restore it.
    When I log out
    And I log in as "admin"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    Then I should see "Course 2"
    When I navigate to "Question bank" in current page administration
    Then I should see "My first reverse pattern match question"

    # Edit the copy and verify the form field contents.
    When I choose "Edit question" action for "My first reverse pattern match question" in the question bank
    Then the following fields match these values:
      | Question name                 | My first reverse pattern match question |
      | id_answer_0                   | The cat sat on the mat                  |
      | id_answer_1                   | The frog sat on the lilly pad           |
      | id_answer_2                   | The dog barked                          |
      | id_fraction_0                 | Should match                            |
      | id_fraction_1                 | Should match                            |
      | id_fraction_2                 | Should not match                        |
      | Hint 1                        | Please try again.                       |
      | Hint 2                        | Don't try too hard with this one.       |
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
