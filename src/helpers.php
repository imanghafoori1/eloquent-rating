<?php

if (! function_exists('getStarCount')) {
    function getStarCount($starable) {
        return Imanghafoori\Stars\Star::getStarCount($starable);
    }
}

if (! function_exists('getAvgRating')) {
    function getAvgRating($starable) {
        return Imanghafoori\Stars\Star::getAvgRating($starable);
    }
}

if (! function_exists('getRatingArray')) {
    function getRatingArray($starCount, $total) {
        return Imanghafoori\Stars\Star::getAvgRating($starCount, $total);
    }
}

if (! function_exists('get_ratings')) {
    function getRatings($starable) {
        return Imanghafoori\Stars\Star::get_ratings($starable);
    }
}

if (! function_exists('rate')) {
    function rate($value, $userId, $starable) {
        Imanghafoori\Stars\Star::rate($value, $userId, $starable);
    }
}
