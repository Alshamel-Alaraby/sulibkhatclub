<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocType extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_doc_types';

    protected $guarded = ['id'];

    public function children()
    {
        return $this->hasMany(DocType::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(DocType::class, 'parent_id', 'id');
    }



    public function statuses()
    {
        return $this->belongsToMany(DocStatus::class, 'arch_document_statuses', 'document_id', 'status_id', 'id', 'id');
    }
    public function archiveFiles()
    {
        return $this->hasMany(ArchiveFile::class, "arch_doc_type_id");
    }



    public function getEmployees()
    {
        $e = $this->archiveFiles()->where('data_type_value', 'like', '');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'arch_type_department', 'arch_doc_type_id', 'arch_department_id')
            ->whereNotNull("key_value");
    }

    public function fields(){

        return $this->belongsToMany(DocumentField::class,'arch_doc_type_fields','doc_type_id','doc_field_id');
    }

    public function doc_type_departments()
    {
        return $this->hasMany(DocTypeDepartment::class, 'arch_doc_type_id', 'id');
    }
    public function doc_type_fields()
    {
        return $this->hasMany(DocTypeField::class, 'doc_type_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'arch_doc_type', 'id');
    }

    public function documentDtls()
    {
        return $this->hasMany(DocumentDtl::class, 'doc_type_id', 'id');
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

        if ($this->archiveFiles()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'archiveFiles',
                'count' => $this->archiveFiles()->count(),
                'ids' => $this->archiveFiles()->pluck('data_type_value')->toArray(),
            ];
        }
        if ($this->doc_type_departments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'doc type departments',
                'count' => $this->doc_type_departments()->count(),
                'ids' => $this->doc_type_departments()->pluck('id')->toArray(),
            ];
        }
        if ($this->doc_type_fields()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'doc type fields',
                'count' => $this->doc_type_fields()->count(),
                'ids' => $this->doc_type_fields()->pluck('id')->toArray(),
            ];
        }
        if ($this->documents()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documents',
                'count' => $this->documents()->count(),
                'ids' => $this->documents()->pluck('id')->toArray(),
            ];
        }
        if ($this->documentDtls()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentDtls',
                'count' => $this->documentDtls()->count(),
                'ids' => $this->documentDtls()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getKeyAttribute()
    {
        return $this->fields()->first();
    }

}
