<?php

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}


$array=array("0"=>array("id"=>"5","total"=>"25"),
			 "1"=>array("id"=>"8","total"=>"180"),
			 "2"=>array("id"=>"2","total"=>"99"),
			 "3"=>array("id"=>"50","total"=>"45")
			);


print_r($array);

$array=array_sort($array, 'total', SORT_ASC);

echo "<br>";
print_r($array);

?>