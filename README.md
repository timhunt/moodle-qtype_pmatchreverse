# Reverse pattern match question type [![Build Status](https://travis-ci.org/timhunt/moodle-qtype_pmatchreverse.svg?branch=master)](https://travis-ci.org/timhunt/moodle-qtype_pmatchreverse)

The pattern match question type allows the teacher to grade sentences entered by
the student using the pattern-match language. This question type does it backwards.
The teacher provides some sentences, and says whether or not they should be matched,
and the student has to come up with a pattern-match expression that does that.
This question type is intended to be used to help people learn pattern-match
syntax.

The pattern-match language is documented at
http://docs.moodle.org/dev/The_OU_PMatch_algorithm


## Acknowledgements

This question type was created by Tim Hunt just for fun.


## Installation

This plugin should be compatible with Moodle 3.4+.

### Install from the plugins database

Install from the Moodle plugins database
* https://moodle.org/plugins/qtype_pmatch
* https://moodle.org/plugins/editor_ousupsub
* https://moodle.org/plugins/qtype_pmatchreverse

### Install using git

To install using git, type these commands in the root of your Moodle install

    git clone https://github.com/moodleou/moodle-qtype_pmatch.git question/type/pmatch
    echo /question/type/pmatch/ >> .git/info/exclude
    git clone https://github.com/moodleou/moodle-editor_ousupsub.git lib/editor/ousupsub
    echo /lib/editor/ousupsub/ >> .git/info/exclude
    git clone https://github.com/timhunt/moodle-qtype_pmatchreverse.git question/type/pmatchreverse
    echo '/question/type/pmatchreverse' >> .git/info/exclude

Then run the moodle update process
Site administration > Notifications
