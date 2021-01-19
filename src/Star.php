<?php

namespace Imanghafoori\Stars;

use App\CoursesModule\Models\Course;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Star extends Model
{
    protected $guarded = ['id'];

    public function starable()
    {
        return $this->morphTo();
    }

    public static function rate($value, $userId, $starable)
    {
        $table = $starable->getTable();
        $id = $starable->getKey();

        $where = [
            'starable_type' => $table,
            'starable_id' => $id,
        ];

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
}
