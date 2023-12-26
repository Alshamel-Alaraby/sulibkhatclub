<?php

namespace Modules\RealEstate\Entities;

use App\Models\DocumentHeaderDetail;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use App\Traits\VideoLink;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstUnit extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait, VideoLink;

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query

        ->select(
                'id',
                'code',
                'name',
                'name_e',
                'description',
                'description_e',
                'unit_area',
                'rooms',
                "unit_net_area",
                'path',
                'floor',
                'properties',
                'module',
                'finishing',
                'view',
                'unit_ty',
                'unit_status_id',
                'building_id',
                )
        ->with('building:id,name,name_e','unitStatus:id,name,name_e','media','type:id,name,name_e','unitFinishing:id,name,name_e','unitView:id,name,name_e','items:id,name,name_e,code_number');
    }

    // relations

    public function unitFinishing()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstFinishing::class, 'finishing');
    }

    public function unitView()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstView::class, 'view');
    }

    public function type()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnitType::class, 'unit_ty');
    }

    public function unitStatus()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstUnitStatus::class, 'unit_status_id');
    }

    public function building()
    {
        return $this->belongsTo(RlstBuilding::class);
    }


    public function items()
    {
        return $this->hasMany(RlstItem::class, 'unit_id');
    }

    public function tenant()
    {
        return $this->hasOne(RlstTenant::class, 'unit_id');
    }

    // public function hasChildren()
    // {
    //     return $this->items()->count() > 0 ;

    // }

    public function documentHeaderDetails()
    {
        return $this->hasMany(DocumentHeaderDetail::class,'unit_id');
    }

    public function contractDetails()
    {
        return $this->hasMany(RlstContractDetail::class, 'unit_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'unit_id');
    }

    public function unitService()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstUnitService::class, 'unit_id');
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->unitService()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'unitService',
                'count' => $this->unitService()->count(),
                'ids' => $this->unitService()->pluck('id')->toArray(),
            ];
        }
        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('id')->toArray(),
            ];
        }
        if ($this->contractDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractDetails',
                'count' => $this->contractDetails()->count(),
                'ids' => $this->contractDetails()->pluck('id')->toArray(),
            ];
        }
        if ($this->items()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'items',
                'count' => $this->items()->count(),
                'ids' => $this->items()->pluck('name')->toArray(),
            ];
        }

        if ($this->tenant()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tenant',
                'count' => $this->tenant()->count(),
                'ids' => $this->tenant()->pluck('name')->toArray(),
            ];
        }
        if ($this->documentHeaderDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document-Header-Details',
                'count' => $this->documentHeaderDetails()->count(),
                'ids' => $this->documentHeaderDetails()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Unit')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    // attributes

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    protected function properties(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    public function scopeGeneralFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->building_id, function ($q) use ($request) {
                    $q->where('building_id', $request->building_id);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_ty, function ($q) use ($request) {
                    $q->where('unit_ty', $request->unit_ty);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_area, function ($q) use ($request) {
                    $q->where('unit_area', $request->unit_area);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->rooms, function ($q) use ($request) {
                    $q->where('rooms', $request->rooms);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->path, function ($q) use ($request) {
                    $q->where('path', $request->path);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->properties, function ($q) use ($request) {
                    $q->whereJsonContains('properties', $request->properties);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->wallet_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->where('wallet_id', $request->wallet_id);
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->owner_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->whereHas('wallet', function ($q) use ($request) {
                                $q->whereHas('walletOwner', function ($q) use ($request) {
                                    $q->where('owner_id', $request->owner_id);
                                });
                            });
                        });
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->country_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->where('country_id', $request->country_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->city_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->where('city_id', $request->city_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->governorate_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                            $q->where('governorate_id', $request->governorate_id);
                    });

                });
            });

        });
    }

    public function scopeUnSoldFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                $q->when($request->unit_status_id, function ($q) use ($request) {
                    $q->whereIn('unit_status_id', $request->unit_status_id);
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->wallet_id, function ($q) use ($request) {
                        $q->whereHas('building', function ($q) use ($request) {
                            $q->whereHas('buildingWallet', function ($q) use ($request) {
                                $q->whereIn('wallet_id', $request->wallet_id);
                            });
                        });
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->owner_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereHas('buildingWallet', function ($q) use ($request) {
                            $q->whereHas('wallet', function ($q) use ($request) {
                                $q->whereHas('walletOwner', function ($q) use ($request) {
                                    $q->whereIn('owner_id', $request->owner_id);
                                });
                            });
                        });
                    });
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->building_id, function ($q) use ($request) {
                        $q->whereIn('building_id', $request->building_id);
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->country_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('country_id', $request->country_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->city_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('city_id', $request->city_id);
                    });
                });
            })->where(function ($q) use ($request) {
                $q->when($request->governorate_id, function ($q) use ($request) {
                    $q->whereHas('building', function ($q) use ($request) {
                        $q->whereIn('governorate_id', $request->governorate_id);
                    });

                });
            })->where(function ($q) use ($request) {
                $q->when($request->properties, function ($q) use ($request) {
                    $q->whereJsonContains('properties', $request->properties);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->unit_area, function ($q) use ($request) {
                    $q->where('unit_area', $request->unit_area);
                });
            })
                ->where(function ($q) use ($request) {
                    $q->when($request->unit_ty, function ($q) use ($request) {
                        $q->where('unit_ty', $request->unit_ty);
                    });
                })->where(function ($q) use ($request) {
                $q->when($request->rooms, function ($q) use ($request) {
                    $q->where('rooms', $request->rooms);
                });
            })->where(function ($q) use ($request) {
                $q->when($request->path, function ($q) use ($request) {
                    $q->where('path', $request->path);
                });
            });

        });
    }

}
