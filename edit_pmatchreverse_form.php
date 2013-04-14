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
 * Defines the editing form for the pmatchreverse question type.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * The pmatchreverse question editing form definition.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchreverse_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $this->add_per_answer_fields($mform, get_string('examplesentencen', 'qtype_pmatchreverse', '{no}'), array(
            1 => get_string('shouldmatch', 'qtype_pmatchreverse'), 0 => get_string('shouldnotmatch', 'qtype_pmatchreverse')));
        $this->add_combined_feedback_fields();
        $this->add_interactive_settings();
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_combined_feedback($question);
        $question = $this->data_preprocessing_hints($question);

        if (empty($question->questiontext['text'])) {
            // Nasty hack to override what the base class does. The way it
            // prepares the questiontext field overwrites the default.
            $question->questiontext['text'] = get_string('defaultquestiontext', 'qtype_pmatchreverse');
        }

        return $question;
    }

    protected function get_more_choices_string() {
        return get_string('addmoresentences', 'qtype_pmatchreverse');
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {

        $answeroptions = array(
            $mform->createElement('text', 'answer', '', array('size' => 40)),
            $mform->createElement('select', 'fraction', '', $gradeoptions),
        );

        $repeated = array(
            $mform->createElement('group', 'answeroptions', $label, $answeroptions, null, false),
        );

        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 1;
        $repeatedoptions['fraction']['disabledif'] = array('answer', 'eq', '');
        $answersoption = 'answers';

        return $repeated;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $uniqueanswers = array();
        $answers = $data['answer'];
        $answercount = 0;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                if (array_key_exists($trimmedanswer, $uniqueanswers)) {
                    $errors['answeroptions[' . $key . ']'] = get_string('examplesmustbeunique', 'qtype_pmatchreverse');
                }
                $uniqueanswers[$trimmedanswer] = 1;
            }
        }
        if ($answercount==0) {
            $errors['answeroptions[0]'] = get_string('examplesentencerequired', 'qtype_pmatchreverse');
        }
        return $errors;
    }

    public function qtype() {
        return 'pmatchreverse';
    }
}
