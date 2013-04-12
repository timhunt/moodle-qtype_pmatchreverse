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
 * pmatchreverse question definition class.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/pmatch/pmatchlib.php');

/**
 * Represents a pmatchreverse question.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchreverse_question extends question_graded_automatically_with_countback {

    /** @var Standard partially correct fields. */
    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;

    /** @var array string => bool, the given sentences, and whether the
     * expression should match them or not. */
    public $sentences = array();

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {
        if (!array_key_exists('answer', $response)) {
            return false;
        }
        $expression = $this->parse_expression($response['answer']);
        return $expression->is_valid();
    }

    public function is_gradeable_response(array $response) {
        return array_key_exists('answer', $response);
    }

    public function get_validation_error(array $response) {
        if (!array_key_exists('answer', $response)) {
            return '';
        }
        return $this->parse_expression($response['answer'])->get_parse_error();
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    public function get_correct_response() {
        return array();
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    /**
     * @param string $currentanswer a response.
     * @return pmatch_expression the equivalent parsed expression.
     */
    public function parse_expression($currentanswer) {
        return new pmatch_expression($currentanswer);
    }

    /**
     * @param string $sentence a response.
     * @param pmatch_expression $expression a pmatch expression, not necessarily valid.
     * @return bool whether the sentence matches the pattern. If invalid, false is returned.
     */
    public function sentence_matches_expression($sentence, $expression) {
        if (!$expression->is_valid()) {
            return false;
        }
        return $expression->matches(new pmatch_parsed_string($sentence));
    }

    public function grade_response(array $response) {
        $expression = new pmatch_expression($response['answer']);

        $numright = 0;
        foreach ($this->sentences as $sentence => $shouldmatch) {
            if (!($this->sentence_matches_expression($sentence, $expression) xor $shouldmatch)) {
                $numright += 1;
            }
        }
        $fraction = $numright / count($this->sentences);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function compute_final_grade($responses, $totaltries) {
        // TODO.
        return 0;
    }
}
