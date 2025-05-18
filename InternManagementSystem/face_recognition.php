
<?php
function euclideanDistance($a, $b) {
    if (count($a) !== count($b)) {
        return INF; // Return a large value if dimensions don't match
    }

    $sum = 0;
    for ($i = 0; $i < count($a); $i++) {
        $sum += pow($a[$i] - $b[$i], 2);
    }

    return sqrt($sum);
}
?>
