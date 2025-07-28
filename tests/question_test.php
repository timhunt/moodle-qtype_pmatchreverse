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

namespace qtype_pmatchreverse;
defined('MOODLE_INTERNAL') || die();

use test_question_maker;
use question_state;
use question_classified_response;
use question_attempt_step;

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the pmatchreverse question definition class.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_pmatchreverse_question
 */
final class question_test extends \advanced_testcase {

    /**
     * Test is response complete.
     */
    public function test_is_complete_response(): void {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertFalse($question->is_complete_response([]));
        $this->assertFalse($question->is_complete_response(['answer' => '']));
        $this->assertFalse($question->is_complete_response(['answer' => 'frog']));
        $this->assertTrue($question->is_complete_response(['answer' => 'match(frog)']));
    }

    /**
     * Test if the response is gradable.
     */
    public function test_is_gradable_response(): void {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertFalse($question->is_gradable_response([]));
        $this->assertFalse($question->is_gradable_response(['answer' => '']));
        $this->assertTrue($question->is_gradable_response(['answer' => 'frog']));
        $this->assertTrue($question->is_gradable_response(['answer' => 'match(frog)']));
    }

    /**
     * Test grading.
     */
    public function test_grading(): void {
        $question = test_question_maker::make_question('pmatchreverse');

        $this->assertEquals([0, question_state::$gradedwrong],
                $question->grade_response(['answer' => 'frog']));
        $this->assertEquals([0, question_state::$gradedwrong],
                $question->grade_response(['answer' => 'match(toad)']));
        $this->assertEquals([0.5, question_state::$gradedpartial],
                $question->grade_response(['answer' => 'match(frog|toad)']));
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response(['answer' => 'match(frog)']));
    }

    /**
     * Test getting the question summary.
     */
    public function test_get_question_summary(): void {
        $q = test_question_maker::make_question('pmatchreverse');
        $this->assertEquals(get_string('matchx', 'qtype_pmatchreverse', 'frog') . '; ' .
                get_string('dontmatchx', 'qtype_pmatchreverse', 'toad'), $q->get_question_summary());
    }

    /**
     * Test summarising responses.
     */
    public function test_summarise_response(): void {
        $q = test_question_maker::make_question('pmatchreverse');
        $summary = $q->summarise_response(['answer' => 'match(frog)']);
        $this->assertEquals('match(frog)', $summary);
    }

    /**
     * Test classifying responses.
     */
    public function test_classify_response(): void {
        $q = test_question_maker::make_question('pmatchreverse');
        $q->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals([0 => question_classified_response::no_response()],
                $q->classify_response(['answer' => '']));

        $this->assertEquals([
                13 => new question_classified_response(0, 'frog', 0),
                14 => new question_classified_response(0, 'frog', 0.5),
            ], $q->classify_response(['answer' => 'frog']));

        $this->assertEquals([
                13 => new question_classified_response(1, 'match(frog)', 0.5),
                14 => new question_classified_response(0, 'match(frog)', 0.5),
            ], $q->classify_response(['answer' => 'match(frog)']));
    }
}
