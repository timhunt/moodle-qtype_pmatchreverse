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
 * Test helpers for the pmatchreverse question type.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the pmatchreverse question type.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 */
class qtype_pmatchreverse_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('frognottoad');
    }

    /**
     * Makes a pmatchreverse question where the student must match frog, but not toad.
     * @return qtype_pmatchreverse_question
     */
    public function make_pmatchreverse_question_frognottoad() {
        question_bank::load_question_definition_classes('pmatchreverse');
        $q = new qtype_pmatchreverse_question();
        test_question_maker::initialise_a_question($q);
        $q->name = 'Match frog but not toad';
        $q->questiontext = 'Please enter a pattern-match expression which matches, or not, the given example sentences.';
        $q->generalfeedback = 'match(frog) is the simplest answer you could have given.';
        test_question_maker::set_standard_combined_feedback_fields($q);
        $q->sentences = array(
            'frog' => 1,
            'toad' => 0,
        );
        $q->sentenceids = array(
            'frog' => 13,
            'toad' => 14,
        );
        $q->hints = array(
            new question_hint(1, 'Hint 1.', FORMAT_HTML),
            new question_hint(2, 'Hint 2.', FORMAT_HTML),
        );
        $q->qtype = question_bank::get_qtype('pmatchreverse');

        return $q;
    }

    /**
     * Gets the question data for a pmatchreverse question where the student must match frog, but not toad.
     * @return stdClass
     */
    public function get_pmatchreverse_question_data_frognottoad() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'pmatchreverse';
        $qdata->name = 'Match frog but not toad';
        $qdata->questiontext = 'Please enter a pattern-match expression which matches, or not, the given example sentences.';
        $qdata->generalfeedback = 'match(frog) is the simplest answer you could have given.';

        $qdata->options = new stdClass();
        test_question_maker::set_standard_combined_feedback_fields($qdata->options);
        unset($qdata->options->shownumcorrect);
        $qdata->options->answers = array(
            13 => new question_answer(13, 'frog', 1, '', FORMAT_HTML),
            14 => new question_answer(14, 'toad', 0, '', FORMAT_HTML),
        );
        $qdata->hints = array(
            new question_hint(1, 'Hint 1.', FORMAT_HTML),
            new question_hint(2, 'Hint 2.', FORMAT_HTML),
        );

        return $qdata;
    }

    /**
     * Gets the form data for a pmatchreverse question where the student must match frog, but not toad.
     * @return stdClass
     */
    public function get_pmatchreverse_question_form_data_frognottoad() {
        $fromform = new stdClass();
        $fromform->penalty = 0.3333333;

        $fromform->qtype = 'pmatchreverse';
        $fromform->name = 'Match frog but not toad';
        $fromform->questiontext = array('text' =>
                'Please enter a pattern-match expression which matches, or not, the given example sentences.',
                'format' => FORMAT_HTML, 'files' => array());
        $fromform->generalfeedback = array('text' => 'match(frog) is the simplest answer you could have given.',
                'format' => FORMAT_HTML, 'files' => array());
        $fromform->correctfeedback = array('text' => test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK,
                'format' => FORMAT_HTML);
        $fromform->partiallycorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                'format' => FORMAT_HTML);
        $fromform->incorrectfeedback = array('text' => test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                'format' => FORMAT_HTML);
        $fromform->answer = array('frog', 'toad');
        $fromform->fraction = array(1, 0);
        $fromform->hint = array(
            array('text' => 'Hint 1.', 'format' => FORMAT_HTML),
            array('text' => 'Hint 2.', 'format' => FORMAT_HTML),
        );

        return $fromform;
    }
}
