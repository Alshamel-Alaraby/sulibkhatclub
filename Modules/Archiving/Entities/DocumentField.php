<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentField extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_document_fields';
    protected $guarded = ['id'];

    public function archiveClosedReference()
    {
        return $this->hasMany(ClosedReference::class, 'docfields_id', 'id');
    }

    public function docTypeField()
    {
        return $this->belongsToMany(DocTypeField::class, 'arch_doc_type_fields', 'doc_type_id', 'doc_field_id')
            ->where("is_reference", 1);
    }

    public function treeProperty()
    {
        return $this->belongsTo(\App\Models\TreeProperty::class, 'tree_property_id', 'id');
    }

    public function dataTye()
    {
        return $this->belongsTo(\App\Models\DataType::class, 'data_type_id', 'id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->archiveClosedReference()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'archive Closed Reference',
                'count' => $this->archiveClosedReference()->count(),
                'ids' => $this->archiveClosedReference()->pluck('field_value')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

}
