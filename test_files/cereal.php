<?php
    if (isset($_GET['array'])) {
        echo "From _GET: ";
        print_r($_GET['array']);
        echo "<br>";
    }
    $array = array("Goose","Magic","Elephants",42);
    echo "Actual array: ";
    print_r($array);
    echo "<br>";

    function arrayToQuery($array,$name) {
        $outstr = "?";
        $first_entry = true;
        foreach($array as $value) {
            $outstr .= $first_entry ? $name."[]=".$value : "&".$name."[]=".$value;
            $first_entry = false;
        }
        return $outstr;
    }
?>

<a href='<?= arrayToQuery($array,"array") ?>'> Click me </a>
