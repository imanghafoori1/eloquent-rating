<?php

if (! function_exists('getStarCount')) {
    function getStarCount($starable, $starType = '_') {
        return Imanghafoori\Stars\Star::getStarCount($starable, $starType = '_');
    }
}

if (! function_exists('getAvgRating')) {
    function getAvgRating($starable, $starType = '_') {
        return Imanghafoori\Stars\Star::getAvgRating($starable, $starType = '_');
    }
}

if (! function_exists('getRatingArray')) {
    function getRatingArray($starCount, $total) {
        return Imanghafoori\Stars\Star::getAvgRating($starCount, $total);
    }
}

if (! function_exists('getRatings')) {
    function getRatings($starable, $starType = '_') {
        return Imanghafoori\Stars\Star::getRatings($starable, $starType = '_');
    }
}

if (! function_exists('star')) {
    function star($value, $userId, $starable, $starType = '_') {
        Imanghafoori\Stars\Star::star($value, $userId, $starable, $starType = '_');
    }
}
