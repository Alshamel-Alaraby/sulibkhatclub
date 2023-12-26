<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class ArchiveFile extends Model implements HasMedia
{
    use HasFactory, LogTrait, SoftDeletes, MediaTrait;
    protected $table = 'arch_archive_files';

    protected $guarded = ['id'];

    public function docType()
    {
        return $this->belongsTo(DocType::class, 'arch_doc_type_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function favourites()
    {
        return $this->hasMany(ArchiveFileFavourite::class, 'arch_archive_file_id');
    }

    protected function dataTypeValue(): Attribute
    {
        return Attribute::make(
            set:fn($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
            get:fn($value) => json_decode($value)
        );
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->favourites()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'favourites',
                'count' => $this->favourites()->count(),
                'ids' => $this->favourites()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


}
