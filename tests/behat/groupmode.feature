@mod_scheduler
Feature: Users can only see their own groups if the scheduler is in group mode
  In order to see slots
  As a user
  I must be allowed to see the group of the relevant teacher

  Background:
    Given the following "users" exist:
      | username   | firstname      | lastname | email                  |
      | edteacher1 | Editingteacher | 1        | edteacher1@example.com |
      | neteacher1 | Nonedteacher   | 1        | neteacher1@example.com |
      | neteacher2 | Nonedteacher   | 2        | neteacher2@example.com |
      | student1   | Student        | 1        | student1@example.com   |
      | student2   | Student        | 2        | student2@example.com   |
      | student3   | Student        | 3        | student3@example.com   |
      | student4   | Student        | 4        | student4@example.com   |
      | student5   | Student        | 5        | student5@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user       | course | role           |
      | edteacher1 | C1     | editingteacher |
      | neteacher1 | C1     | teacher        |
      | neteacher2 | C1     | teacher        |
      | student1   | C1     | student        |
      | student2   | C1     | student        |
      | student3   | C1     | student        |
      | student4   | C1     | student        |
      | student5   | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | GA       |
      | Group B | C1     | GB       |
      | Group C | C1     | GC       |
    And the following "group members" exist:
      | user       | group |
      | edteacher1 | GA    |
      | edteacher1 | GB    |
      | neteacher1 | GB    |
      | neteacher1 | GC    |
      | student1   | GA    |
      | student2   | GA    |
      | student3   | GB    |  
      | student4   | GB    |
    And the following "activities" exist:
      | activity  | name                    | intro | course | idnumber   | groupmode |
      | scheduler | Test scheduler none     | n     | C1     | schedulern | 0         |
      | scheduler | Test scheduler separate | n     | C1     | schedulers | 1         |
      | scheduler | Test scheduler visible  | n     | C1     | schedulerv | 2         |
    And the following "permission overrides" exist:
      | capability                  | permission | role    | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | teacher | Course       | C1        |
    And I log in as "edteacher1"
    And I follow "Course 1"
    And I add 5 slots 10 days ahead in "Test scheduler none" scheduler and I fill the form with:
      | Location  | Here |
    And I follow "Course 1"
    And I add 5 slots 11 days ahead in "Test scheduler visible" scheduler and I fill the form with:
      | Location  | Here |
    And I follow "Course 1"
    And I add 5 slots 12 days ahead in "Test scheduler separate" scheduler and I fill the form with:
      | Location  | Here |
    And I log out
    And I log in as "neteacher1"
    And I follow "Course 1"
    And I add 5 slots 10 days ahead in "Test scheduler none" scheduler and I fill the form with:
      | Location  | There |
    And I follow "Course 1"
    And I add 5 slots 11 days ahead in "Test scheduler visible" scheduler and I fill the form with:
      | Location  | There |
    And I follow "Course 1"
    And I add 5 slots 12 days ahead in "Test scheduler separate" scheduler and I fill the form with:
      | Location  | There |
    And I log out
    And I add the upcoming events block globally
    
  @javascript
  Scenario: Editing teachers can see all slots and all groups
    When I log in as "edteacher1"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    And I follow "Statistics"
    And I follow "All appointments"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"

    When I follow "Course 1"
    And I follow "Test scheduler visible"
    And I follow "Statistics"
    And I follow "All appointments"
    Then I should see "Visible groups"
    And the "group" select box should contain "All participants"
    And the "group" select box should contain "Group A"
    And the "group" select box should contain "Group B"
    And the "group" select box should contain "Group C"
    When I set the field "group" to "All participants"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group A"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should not see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group B"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group C"
    Then I should not see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"

    When I follow "Course 1"
    And I follow "Test scheduler separate"
    And I follow "Statistics"
    And I follow "All appointments"
    Then I should see "Separate groups"
    And the "group" select box should contain "All participants"
    And the "group" select box should contain "Group A"
    And the "group" select box should contain "Group B"
    And the "group" select box should contain "Group C"
    When I set the field "group" to "All participants"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group A"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should not see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group B"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group C"
    Then I should not see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"

    # In the "My appointments" tab, the teacher should only see students to schedule from their groups,
    # i.e., groups 1 and 2.
    # Students outside any group should not be visible.
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "Student 1" in the "studentstoschedule" "table"
    And I should see "Student 3" in the "studentstoschedule" "table"
    Then I should not see "Student 5" in the "studentstoschedule" "table"
    And I log out
    
  @javascript
  Scenario: Nonediting teachers can see groups only if allowed by the group mode
    
    When I log in as "neteacher1"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "4 students still need to make an appointment"
    When I follow "All appointments"
    Then I should see "Editingteacher 1" in the "slotmanager" "table" 
    And I should see "Nonedteacher 1" in the "slotmanager" "table"

    When I follow "Course 1"
    And I follow "Test scheduler visible"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "2 students still need to make an appointment"
    When I follow "All appointments"
    Then I should see "Visible groups"
    And the "group" select box should contain "All participants"
    And the "group" select box should contain "Group A"
    And the "group" select box should contain "Group B"
    And the "group" select box should contain "Group C"
    When I set the field "group" to "All participants"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group A"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should not see "Nonedteacher 1" in the "slotmanager" "table"    
    When I set the field "group" to "Group B"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group C"
    Then I should not see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "2 students still need to make an appointment"
    When I follow "All appointments"
    Then I should see "Separate groups"
    And the "group" select box should not contain "All participants"
    And the "group" select box should not contain "Group A"
    And the "group" select box should contain "Group B"
    And the "group" select box should contain "Group C"
    When I set the field "group" to "Group B"
    Then I should see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"
    When I set the field "group" to "Group C"
    Then I should not see "Editingteacher 1" in the "slotmanager" "table"
    And I should see "Nonedteacher 1" in the "slotmanager" "table"

    # In the "My appointments" tab, the teacher should only see students to schedule from their groups,
    # i.e., group 1.
    # Students in group 2 and outside any group should not be visible.
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "Student 1" in the "studentstoschedule" "table"
    And I should see "Student 2" in the "studentstoschedule" "table"
    And I should not see "Student 3" in the "studentstoschedule" "table"
    And I should not see "Student 4" in the "studentstoschedule" "table"
    Then I should not see "Student 5" in the "studentstoschedule" "table"
    And I log out

    # neteacher2 sees no students for scheduling in group mode, since he's not member of a group

    When I log in as "neteacher2"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "4 students still need to make an appointment"

    When I follow "Course 1"
    And I follow "Test scheduler visible"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "No students available for scheduling"

    When I follow "Course 1"
    And I follow "Test scheduler separate"
    And I follow "Statistics"
    And I follow "My appointments"
    Then I should see "No students available for scheduling"    
    
  @javascript
  Scenario: Students can see slots available to their own groups, or a slots if group mode is off
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    Then I should see "Editingteacher 1"
    And I should see "Nonedteacher 1"
    When I follow "Course 1"
    And I follow "Test scheduler visible"
    Then I should see "Editingteacher 1"
    And I should not see "Nonedteacher 1"
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    Then I should see "Editingteacher 1"
    And I should not see "Nonedteacher 1"
    And I log out

    When I log in as "student3"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    Then I should see "Editingteacher 1"
    And I should see "Nonedteacher 1"
    When I follow "Course 1"
    And I follow "Test scheduler visible"
    Then I should see "Editingteacher 1"
    And I should see "Nonedteacher 1"
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    Then I should see "Editingteacher 1"
    And I should see "Nonedteacher 1"
    And I log out

    When I log in as "student5"
    And I follow "Course 1"
    And I follow "Test scheduler none"
    Then I should see "Editingteacher 1"
    And I should see "Nonedteacher 1"
    When I follow "Course 1"
    And I follow "Test scheduler visible"
    Then I should see "No appointment required"
    When I follow "Course 1"
    And I follow "Test scheduler separate"
    Then I should see "No appointment required"
    And I log out
