<?php

namespace App\Game;

use App\Aliyunoss\Manager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Shelf extends Model
{
    //
    protected $table = "shelf";
    protected $primaryKey = "gameId";
    public $timestamps = false;

    protected $ossManager;

    public function __construct(array $attributes = []) {
        $this->ossManager = new Manager();
        parent::__construct($attributes);
    }

    // 获取subject
    public function getSubject($officialGameIds) {
//        DB::connection()->enableQueryLog();
        $subj = new Subjects();
        $data = $subj->whereIn('officialGameId', explode(",", $officialGameIds))->get();
//        var_dump(DB::getQueryLog());
        foreach ($data as &$item) {
            $item->attributes["latestPriceCNY"] = $item->getLastPriceCNY();
        }
        unset($item);
        return $data;
    }

    public function loadCoverUrl() {
        $this->attributes["mp_cover"] = [];
        $this->attributes["mp_cover_detail"] = [];
        // json串的单引号问题兼容
        $this->cover = str_replace("'", "\"", $this->cover);

        if ($covers = json_decode($this->cover, true)) {
            foreach ($covers as $c) {
                $this->attributes["mp_cover"][] = $this->ossManager->getUrl($c, "cover", "mp_list_icon_w60h60");
                $this->attributes["mp_cover_detail"][] = $this->ossManager->getUrl($c, "cover", "mp_detail_pic_w414");
            }
        }

        return $this;
    }

    public function loadThumbUrl() {
        $this->attributes["mp_thumb"] = [];
        // json串的单引号问题兼容
        $this->thumb = str_replace("'", "\"", $this->thumb);

        if ($thumbs = json_decode($this->thumb, true)) {
            foreach ($thumbs as $t) {
                $this->attributes["mp_thumb"][] = $this->ossManager->getUrl($t, "thumb", "mp_detail_pic_w414h240");
            }
        }
        return $this;
    }

}
