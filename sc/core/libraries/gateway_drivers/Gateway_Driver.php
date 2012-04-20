<?php

    class SC_Gateway_Driver {
        function __construct($name=FALSE) {
            global $SC;
            $this->SC = $SC;
            if ($name) {
                $this->name = $name;
            }
        }
    }
