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
 * Version information for the pmatchreverse question type.
 *
 * @package   qtype_pmatchreverse
 * @copyright 2013 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2026072000;
$plugin->requires  = 2025100600; // Requires Moodle 5.1.
$plugin->component = 'qtype_pmatchreverse';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = 'v1.8 for Moodle 5.2+';
$plugin->supported = [501, 502];

$plugin->dependencies = [
    'qtype_pmatch' => 2022080900,
];

$plugin->outestssufficient = true;
