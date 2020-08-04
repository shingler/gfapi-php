<?php
/**
 * 阿里云oss配置
 */
namespace App\Aliyunoss;

return [

    /**
     * bucket设置
     * 格式：[bucket_key => ["bucket"=>bucket名, "domain"=>域名, "style"=>[图片缩放样式]]
     */
    "OSS_BUCKETNAME" => [
        // 封图
        "cover" => [
            "bucket" => 'gf-cover',
            "domain" => "gf-cover.oss-cn-beijing.aliyuncs.com",
            "style" => ["list_icon_w200", "list_icon_w400", "detail_pic_w500",
            "mp_list_icon_w60h60", "mp_detail_pic_w414"]
        ],
        // 截图
        "thumb" => [
            "bucket" => 'gf-thumb',
            "domain" => "gf-thumb.oss-cn-beijing.aliyuncs.com",
            "style" => ["detail_pic_w500", "mp_detail_pic_w414h240"]
        ],
        // 头像
        "avatar" => [
            "bucket" => 'gf-avatar',
            "domain" => "gf-avatar.oss-cn-beijing.aliyuncs.com",
            "style" => ["avatar_middle_w80h80"]
        ],
    ],
];
