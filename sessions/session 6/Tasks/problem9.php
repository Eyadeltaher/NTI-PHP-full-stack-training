<?php
$tests = array("tariq", 1.5, true, 7, "s", false);

foreach ($tests as $t) {
    if (is_bool($t)) {
        echo $t ? "Yes " : "No";
    }
    else {
        echo $t . " ";
    }
}
?>