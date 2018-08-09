# Reverse pattern match question type

https://moodle.org/plugins/qtype_pmatchreverse

This question type was created by Tim Hunt just for fun.

The pattern match question type allows the teacher to grade sentences entered by
the student using the pattern-match language. This question type does it backwards.
The teacher provides some sentences, and says whether or not they should be matched,
and the student has to come up with a pattern-match expression that does that.
This question type is intended to be used to help people learn pattern-match
syntax.

The pattern-match language is documented at
http://docs.moodle.org/dev/The_OU_PMatch_algorithm

## Installation

Either isntall from the Moodle plugins database, using the link above, or you can
install the question type using git, type this command in the root of your
Moodle install

    git clone git://github.com/timhunt/moodle-qtype_pmatchreverse.git question/type/pmatchreverse
    echo '/question/type/pmatchreverse' >> .git/info/exclude

Note that you also need the pmatch question type installed:

    git clone git://github.com/moodleou/moodle-editor_ousupsub.git lib/editor/ousupsub
    echo '/lib/editor/ousupsub' >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-qtype_pmatch.git question/type/pmatch
    echo '/question/type/pmatch' >> .git/info/exclude

Once the code is in place, remember to visit the Site administration -> Notifications
page to complete the install.
