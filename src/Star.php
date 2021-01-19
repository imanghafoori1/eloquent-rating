<?php

namespace Imanghafoori\Stars;

use App\CoursesModule\Models\Course;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Star
{
    public static function getStarCount($starable)
    {
        $where = self::getWhere($starable);

        DB::table('star_stats')
            ->select('star_count')
            ->where($where)
            ->value('star_count');
    }

    public static function getAvgRating($starable)
    {
        $where = self::getWhere($starable);

        DB::table('star_stats')
            ->select('avg_value')
            ->where($where)
            ->value('avg_value');
    }

    private static function getRatingArray($starCount, $total)
    {
        $percent =  (100 * $starCount) / $total;

        return [
            'count' => $starCount,
            'percent' => number_format($percent),
        ];
    }

    public static function get_ratings($starable)
    {
        $where = self::getWhere($starable);

        $star = DB::table('star_stats')->where($where)->first();
        $total = $star->star_count;

        $ratings = [
            'rating_count' => $total,
            'rating_avg' => $star->avg_value,
            'stats' => [
                1 => self::getRatingArray($star->one_star_count, $total),
                2 => self::getRatingArray($star->two_star_count, $total),
                3 => self::getRatingArray($star->three_star_count, $total),
                4 => self::getRatingArray($star->four_star_count, $total),
                5 => self::getRatingArray($star->five_star_count, $total),

            ],
        ];

        return $ratings;
    }

    public static function rate($value, $userId, $starable)
    {
        $where = self::getWhere($starable);

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

    public static function insertStats($where, $value)
    {
        $countOne = [
            ['one_star_count' => 1],
            ['two_star_count' => 1],
            ['three_star_count' => 1],
            ['four_star_count' => 1],
            ['five_star_count' => 1],
        ][$value];

        $data = [
            'avg_value' => $value,
            'star_count' => 1,
        ] + $where + $countOne;

        DB::table('star_stats')->insert($data);
    }

    public static function insertStar($where, $user, $rating)
    {
        DB::table('stars')->insert($where + $user + $rating);
    }

    public static function updateStats($where, $value)
    {
        $increment = [
            ['one_star_count' => DB::raw('one_star_count + 1')],
            ['two_star_count' => DB::raw('two_star_count + 1')],
            ['three_star_count' => DB::raw('three_star_count + 1')],
            ['four_star_count' => DB::raw('four_star_count + 1')],
            ['five_star_count' => DB::raw('five_star_count + 1')],
        ][$value];

        $data = [
            'avg_value' => DB::table('stars')->where($where)->avg('rating'),
            'star_count' => DB::raw('star_count + 1'),
        ] + $increment;


        DB::table('star_stats')->where($where)->update($data);
    }

    public static function updateStar($where, $user, $rating)
    {
        DB::table('stars')->where($where + $user)->update($rating);
    }

    public static function getWhere($starable)
    {
        return [
            'starable_id' => $starable->getKey(),
            'starable_type' => $starable->getTable(),
        ];
    }
}
