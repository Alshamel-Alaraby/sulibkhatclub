<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocStatus extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_doc_statuses';
    protected $guarded = ['id'];

    // relations

    public function files()
    {
        return $this->belongsToMany(ArchiveFile::class, 'arch_archive_files', 'arch_doc_status_id', 'arch_doc_type_id');
    }

    public function archDocumentStatuses()
    {
        return $this->hasMany(ArchDocumentStatus::class, 'status_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'doc_status', 'id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->archDocumentStatuses()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'arch Document Statuses',
                'count' => $this->archDocumentStatuses()->count(),
                'ids' => $this->archDocumentStatuses()->pluck('id')->toArray(),
            ];
        }
        if ($this->documents()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documents',
                'count' => $this->documents()->count(),
                'ids' => $this->documents()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

}
