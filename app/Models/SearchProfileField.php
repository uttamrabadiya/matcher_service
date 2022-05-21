<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchProfileField extends Model
{
    use HasFactory;
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        self::creating(function($model){
            if ($model->min_value && is_numeric($model->min_value)) {
                $model->loose_min_value = ($model->min_value - (($model->min_value * 25) / 100));
            } else {
                $model->loose_min_value = null;
            }
            if ($model->max_value && is_numeric($model->max_value)) {
                $model->loose_max_value = ($model->max_value + (($model->max_value * 25) / 100));
            } else {
                $model->loose_max_value = null;
            }
        });
    }

    public function searchProfile()
    {
        return $this->belongsTo(SearchProfile::class);
    }

    public function scopeMatchMinValue($query, $fieldValue)
    {
        return $query->where(function($query) use($fieldValue) {
            $query->whereNull('loose_min_value')->orWhere('loose_min_value', '<=', $fieldValue);
        });
    }

    public function scopeMatchMaxValue($query, $fieldValue)
    {
        return $query->where(function($query) use($fieldValue) {
            $query->whereNull('loose_max_value')->orWhere('loose_max_value', '>=', $fieldValue);
        });
    }

    public function scopeMatchField($query, $field, $fieldValue)
    {
        return $query->where('field', $field)
            ->matchMinValue($fieldValue)
            ->matchMaxValue($fieldValue);
    }
}
