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
                $question->grade_response(array('answer' => 'match(frog)')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 'match(frog|toad)')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => 'match(frog)')));
    }

    public function test_get_question_summary() {
        $sa = test_question_maker::make_question('pmatchreverse');
        // TODO
        $qsummary = $sa->get_question_summary();
        $this->assertEquals('Name an amphibian: __________', $qsummary);
    }

    public function test_summarise_response() {
        $sa = test_question_maker::make_question('pmatchreverse');
        $summary = $sa->summarise_response(array('answer' => 'match(frog)'));
        $this->assertEquals('match(frog)', $summary);
    }

    public function test_classify_response() {
        $sa = test_question_maker::make_question('pmatchreverse');
        $sa->start_attempt(new question_attempt_step(), 1);
        // TODO

        $this->assertEquals(array(
                new question_classified_response(13, 'frog', 1.0)),
                $sa->classify_response(array('answer' => 'frog')));
        $this->assertEquals(array(
                new question_classified_response(14, 'toad', 0.8)),
                $sa->classify_response(array('answer' => 'toad')));
        $this->assertEquals(array(
                new question_classified_response(15, 'cat', 0.0)),
                $sa->classify_response(array('answer' => 'cat')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $sa->classify_response(array('answer' => '')));
    }
}
