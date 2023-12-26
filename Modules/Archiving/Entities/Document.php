<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_documents';
    protected $guarded = ['id'];

    public function archDocType()
    {
        return $this->belongsTo(DocType::class, 'arch_doc_type', 'id');
    }
    public function docStatus()
    {
        return $this->belongsTo(DocStatus::class, 'doc_status', 'id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function archDocumentStatuses()
    {
        return $this->hasMany(ArchDocumentStatus::class, 'document_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->archDocumentStatuses()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'arch Document Statuses',
                'count' => $this->archDocumentStatuses()->count(),
                'ids' => $this->archDocumentStatuses()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }



}
