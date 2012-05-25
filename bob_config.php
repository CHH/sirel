<?php

namespace Bob;

task('default', array('test'));

task('test', function() {
    sh(array("phpunit", "--coverage-html", "coverage/"));
    sh(array("open", "coverage/index.html"));
});
