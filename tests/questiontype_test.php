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
 * Unit tests for the pmatchreverse question type class.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/pmatchreverse/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the shortanswer question type class.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_pmatchreverse
 */
class qtype_pmatchreverse_test extends question_testcase {

    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_pmatchreverse();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEquals(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    protected function get_test_question_data() {
        return test_question_maker::get_question_data('pmatchreverse');
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'pmatchreverse');
    }

    public function test_can_analyse_responses() {
        $this->assertTrue($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $q = test_question_maker::get_question_data('pmatchreverse');
        $this->assertEquals(0, $this->qtype->get_random_guess_score($q));
    }

    public function test_get_possible_responses() {
        $q = test_question_maker::get_question_data('pmatchreverse');

        $this->assertEquals(array(
            13 => array(
                1 => new question_possible_response(get_string('matchesx', 'qtype_pmatchreverse', 'frog'), 0.5),
                0 => new question_possible_response(get_string('doesnotmatchex', 'qtype_pmatchreverse', 'frog'), 0),
            ),
            14 => array(
                1 => new question_possible_response(get_string('matchesx', 'qtype_pmatchreverse', 'toad'), 0),
                0 => new question_possible_response(get_string('doesnotmatchex', 'qtype_pmatchreverse', 'toad'), 0.5),
            ),
            0 => array(question_possible_response::no_response()),
        ), $this->qtype->get_possible_responses($q));
    }

    public function test_xml_import() {
        $xml = '  <question type="pmatchreverse">
    <name>
      <text>Match frog but not toad</text>
    </name>
    <questiontext format="html">
      <text>Please enter a pattern-match expression which matches, or not, the given example sentences.</text>
    </questiontext>
    <generalfeedback>
      <text>match(frog) is the simplest answer you could have given.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <correctfeedback>
      <text>Well done!</text>
    </correctfeedback>
    <partiallycorrectfeedback>
      <text>Parts, but only parts, of your response are correct.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback>
      <text>That is not right at all.</text>
    </incorrectfeedback>
    <answer fraction="100">
      <text>frog</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <answer fraction="0">
      <text>toad</text>
      <feedback>
        <text></text>
      </feedback>
    </answer>
    <hint>
      <text>Hint 1.</text>
    </hint>
    <hint>
      <text>Hint 2.</text>
    </hint>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'ddwtos');

        $expectedq = test_question_maker::get_question_form_data('pmatchreverse');
        $expectedq->questiontextformat = $expectedq->questiontext['format'];
        $expectedq->questiontext = $expectedq->questiontext['text'];
        $expectedq->generalfeedbackformat = $expectedq->generalfeedback['format'];
        $expectedq->generalfeedback = $expectedq->generalfeedback['text'];

        // Redundant asserts to give better failure messages.
        $this->assertEquals($expectedq->questiontext, $q->questiontext);
        $this->assertEquals($expectedq->correctfeedback, $q->correctfeedback);
        $this->assertEquals($expectedq->answer, $q->answer);
        $this->assertEquals($expectedq->hint, $q->hint);
        $this->assert(new question_check_specified_fields_expectation($expectedq), $q);
    }

    public function test_xml_export() {
        $qdata = test_question_maker::get_question_data('pmatchreverse');

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 0  -->
  <question type="pmatchreverse">
    <name>
      <text>Match frog but not toad</text>
    </name>
    <questiontext format="html">
      <text>Please enter a pattern-match expression which matches, or not, the given example sentences.</text>
    </questiontext>
    <generalfeedback format="html">
      <text>match(frog) is the simplest answer you could have given.</text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <correctfeedback format="html">
      <text>Well done!</text>
    </correctfeedback>
    <partiallycorrectfeedback format="html">
      <text>Parts, but only parts, of your response are correct.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="html">
      <text>That is not right at all.</text>
    </incorrectfeedback>
    <answer fraction="100" format="plain_text">
      <text>frog</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <answer fraction="0" format="plain_text">
      <text>toad</text>
      <feedback format="html">
        <text></text>
      </feedback>
    </answer>
    <hint format="html">
      <text>Hint 1.</text>
    </hint>
    <hint format="html">
      <text>Hint 2.</text>
    </hint>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }
}
