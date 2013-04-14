<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the pmatchreverse question definition class.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the pmatchreverse question definition class.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_pmatchreverse
 */
class qtype_pmatchreverse_question_test extends advanced_testcase {

    public function test_is_complete_response() {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(array('answer' => '')));
        $this->assertFalse($question->is_complete_response(array('answer' => 'frog')));
        $this->assertTrue($question->is_complete_response(array('answer' => 'match(frog)')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(array('answer' => '')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'frog')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'match(frog)')));
    }

    public function test_grading() {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 'frog')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => 'match(toad)')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 'match(frog|toad)')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 'match(frog)')));
    }

    public function test_get_question_summary() {
        $q = test_question_maker::make_question('pmatchreverse');
        $this->assertEquals(get_string('matchx', 'qtype_pmatchreverse', 'frog') . '; ' .
                get_string('dontmatchx', 'qtype_pmatchreverse', 'toad'), $q->get_question_summary());
    }

    public function test_summarise_response() {
        $q = test_question_maker::make_question('pmatchreverse');
        $summary = $q->summarise_response(array('answer' => 'match(frog)'));
        $this->assertEquals('match(frog)', $summary);
    }

    public function test_classify_response() {
        $q = test_question_maker::make_question('pmatchreverse');
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(0 => question_classified_response::no_response()),
                $q->classify_response(array('answer' => '')));

        $this->assertEquals(array(
                13 => new question_classified_response(0, 'frog', 0),
                14 => new question_classified_response(0, 'frog', 0.5),
            ), $q->classify_response(array('answer' => 'frog')));

        $this->assertEquals(array(
                13 => new question_classified_response(1, 'match(frog)', 0.5),
                14 => new question_classified_response(0, 'match(frog)', 0.5),
            ), $q->classify_response(array('answer' => 'match(frog)')));
    }
}
