<?php
namespace App\Models;

class Sermon extends ContentModel
{
    protected const TABLE  = 'sermons';
    protected const FIELDS = ['title','slug','excerpt','content','bible_reference','video_url','image','status','published_at'];
}
