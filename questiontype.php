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
 * Question type class for the pmatchreverse question type.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');


/**
 * The pmatchreverse question type.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchreverse extends question_type {

    public function extra_question_fields() {
        return array('qtype_pmatchreverse_options', 'correctfeedback', 'correctfeedbackformat',
                'partiallycorrectfeedback', 'partiallycorrectfeedbackformat',
                'incorrectfeedback', 'incorrectfeedbackformat');
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Insert all the new answers.
        foreach ($question->answer as $key => $answerdata) {
            if (trim($answerdata) == '') {
                continue;
            }

            if ($answer = array_shift($oldanswers)) {
                $answer->answer   = trim($answerdata);
                $answer->fraction = isset($question->fraction[$key]) && ((float) $question->fraction[$key]) > 0;
                $DB->update_record('question_answers', $answer);

            } else {
                $answer = new stdClass();
                $answer->question       = $question->id;
                $answer->answer         = trim($answerdata);
                $answer->answerformat   = FORMAT_PLAIN;
                $answer->fraction       = isset($question->fraction[$key]) && ((float) $question->fraction[$key]) > 0;
                $answer->feedback       = '';
                $answer->feedbackformat = FORMAT_HTML;
                $DB->insert_record('question_answers', $answer);
            }
        }

        // Delete old answer records.
        foreach ($oldanswers as $oa) {
            $DB->delete_records('question_answers', array('id' => $oa->id));
        }

        // Save combined feedback.
        $options = $DB->get_record('qtype_pmatchreverse_options',
                array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('qtype_pmatchreverse_options', $options);
        }

        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('qtype_pmatchreverse_options', $options);

        $this->save_hints($question);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        foreach ($questiondata->options->answers as $answer) {
            $question->sentences[$answer->answer] = ($answer->fraction != 0);
            $question->sentenceids[$answer->answer] = $answer->id;
        }
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    public function get_random_guess_score($questiondata) {
        return 0;
    }

    public function get_possible_responses($questiondata) {
        $numparts = count($questiondata->options->answers);
        $parts = array();
        foreach ($questiondata->options->answers as $answer) {
            $shouldmatch = (int) $answer->fraction;
            $parts[$answer->id] = array(
                1 => new question_possible_response(get_string('matchesx', 'qtype_pmatchreverse', $answer->answer),
                        $shouldmatch / $numparts),
                0 => new question_possible_response(get_string('doesnotmatchex', 'qtype_pmatchreverse', $answer->answer),
                        (!$shouldmatch) / $numparts),
            );
        }

        $parts[0] = array(question_possible_response::no_response());

        return $parts;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != $this->name()) {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = $this->name();

        // Run through the answers.
        $answers = $data['#']['answer'];
        $acount = 0;
        foreach ($answers as $answer) {
            $ans = $format->import_answer($answer, false, $format->get_format(FORMAT_PLAIN));
            $question->answer[$acount]   = $ans->answer['text'];
            $question->fraction[$acount] = $ans->fraction;
            ++$acount;
        }

        $format->import_combined_feedback($question, $data);
        $format->import_hints($question, $data, false, false,
                $format->get_format($question->questiontextformat));

        return $question;
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        $output = '';
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        $output .= $format->write_answers($question->options->answers);
        return $output;
    }
}
