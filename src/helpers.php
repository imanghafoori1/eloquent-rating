<?php

if (! function_exists('getStarCount')) {
    function getStarCount($starable, $starType = '_')
    {
        return Imanghafoori\Stars\Star::getStarCount($starable, $starType = '_');
    }
}

if (! function_exists('getAvgRating')) {
    function getAvgRating($starable, $starType = '_')
    {
        return Imanghafoori\Stars\Star::getAvgRating($starable, $starType = '_');
    }
}

if (! function_exists('getRatings')) {
    function getRatings($starable, $starType = '_')
    {
        return Imanghafoori\Stars\Star::getRatings($starable, $starType = '_');
    }
}
