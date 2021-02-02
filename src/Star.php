<?php

namespace Imanghafoori\Stars;

use Illuminate\Support\Facades\DB;

class Star
{
    public static function getStarCount($starable, $starType = '_')
    {
        $where = self::getWhere($starable, $starType);

        return self::starStatTable()
            ->select('star_count')
            ->where($where)
            ->value('star_count');
    }

    public static function getAvgRating($starable, $starType = '_')
    {
        $where = self::getWhere($starable, $starType);

        return self::starStatTable()
            ->select('avg_value')
            ->where($where)
            ->value('avg_value');
    }

    private static function getRatingArray($starCount, $total)
    {
        $percent =  (100 * $starCount) / $total;

        return [
            'count' => $starCount,
            'percent' => number_format($percent, config('stars.percent_decimal_count' , 0)),
        ];
    }

    public static function getRatings($starable, $starType = '_')
    {
        $starStat = self::starStatTable()->where(self::getWhere($starable, $starType))->first();
        $total = $starStat->star_count;

        return [
            'star_count' => $total,
            'avg' => $starStat->avg_value,
            'stats' => [
                '1' => self::getRatingArray($starStat->one_star_count, $total),
                '2' => self::getRatingArray($starStat->two_star_count, $total),
                '3' => self::getRatingArray($starStat->three_star_count, $total),
                '4' => self::getRatingArray($starStat->four_star_count, $total),
                '5' => self::getRatingArray($starStat->five_star_count, $total),
            ],
        ];
    }

    public static function star($value, $userId, $starable, $starType = '_')
    {
        $where = self::getWhere($starable, $starType);

        $user = ['user_id' => $userId];
        $rating = ['value' => $value];

        $has = DB::table('stars')->where($where + $user)->exists();

        DB::beginTransaction();
        if ($has) {
            self::updateStar($where, $user, $rating);
            self::updateStats($where, $value);
        } else {
            self::insertStar($where, $user, $rating);
            self::insertStats($where, $value);
        }
        DB::commit();
    }

    private static function insertStats($where, $value)
    {
        $countOne = [
            ['_'],
            ['one_star_count' => 1],
            ['two_star_count' => 1],
            ['three_star_count' => 1],
            ['four_star_count' => 1],
            ['five_star_count' => 1],
        ][(int) $value];

        $data = [
            'avg_value' => $value,
            'star_count' => 1,
        ] + $where + $countOne;

        DB::table('star_stats')->insert($data);
    }

    private static function insertStar($where, $user, $rating)
    {
        DB::table('stars')->insert($where + $user + $rating);
    }

    private static function updateStats($where, $value)
    {
        $increment = [
            ['_'],
            ['one_star_count' => DB::raw('one_star_count + 1')],
            ['two_star_count' => DB::raw('two_star_count + 1')],
            ['three_star_count' => DB::raw('three_star_count + 1')],
            ['four_star_count' => DB::raw('four_star_count + 1')],
            ['five_star_count' => DB::raw('five_star_count + 1')],
        ][(int) $value];

        $data = [
            'avg_value' => DB::table('stars')->where($where)->avg('rating'),
            'star_count' => DB::raw('star_count + 1'),
        ] + $increment;


        self::starStatTable()->where($where)->update($data);
    }

    private static function updateStar($where, $user, $rating)
    {
        DB::table('stars')->where($where + $user)->update($rating);
    }

    private static function getWhere($starable, $starType)
    {
        return [
            'starable_id' => $starable->getKey(),
            'starable_type' => $starable->getTable(),
            'star_type' => $starType,
        ];
    }

    private static function starStatTable()
    {
        return DB::table('star_stats');
    }
}
