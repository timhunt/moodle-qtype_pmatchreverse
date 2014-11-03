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
 * pmatchreverse question renderer class.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for pmatchreverse questions.
 *
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_pmatchreverse_renderer extends qtype_with_combined_feedback_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question      = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        $inputname     = $qa->get_qt_field_name('answer');
        if ($currentanswer) {
            $expression = $question->parse_expression($currentanswer);
        } else {
            $expression = new pmatch_expression('');
        }

        // Question text.
        $result = html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));

        // Input.
        $attributes = array(
            'id'    => $inputname,
            'name'  => $inputname,
            'class' => 'qtype_pmatchreverse_response',
            'rows'  => 5,
            'cols'  => 60,
        );
        if ($options->readonly) {
            $attributes['readonly'] = 'readonly';
        }
        $label = html_writer::label(get_string('answer', 'question'), $inputname, false, array('class' => 'accesshide'));
        $input = html_writer::tag('textarea', s($currentanswer), $attributes);
        $result .= html_writer::tag('div', $label . $input, array('class' => 'ablock'));

        // Any validation error.
        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }

        // Table of sentences and whether they should / do match.
        $table = new html_table();
        $table->head = array(get_string('sentence', 'qtype_pmatchreverse'), get_string('shouldmatch', 'qtype_pmatchreverse'));
        if ($options->correctness || $options->feedback) {
            $table->head[] = get_string('doesmatch', 'qtype_pmatchreverse');
        }
        foreach ($question->sentences as $sentence => $shouldmatch) {
            $row = new html_table_row();
            $row->cells = array(
                new html_table_cell(s($sentence)),
                new html_table_cell($this->display_bool($shouldmatch)),
            );
            if ($options->correctness || $options->feedback) {
                $doesmatch = $question->sentence_matches_expression($sentence, $expression);
                $row->cells[] = new html_table_cell($this->display_bool($doesmatch));
                $row->attributes['class'] = $this->feedback_class(
                        (float) $question->compare_bools($shouldmatch, $doesmatch));
            }
            $table->data[] = $row;
        }
        $result .= html_writer::table($table);

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    /**
     * @param bool $bool a boolean value.
     * @return string Yes or No.
     */
    protected function display_bool($bool) {
        if ($bool) {
            return get_string('yes');
        } else {
            return get_string('no');
        }
    }
}
