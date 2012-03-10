<?php

require 'core/init.php';

$SC->load_library('Items');

print_r($SC->Items->option_flag(5,'allow_qty'));

