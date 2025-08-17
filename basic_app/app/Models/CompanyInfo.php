<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class CompanyInfo extends Model
{
        use HasFactory, Notifiable;

    //
    protected $table = 'company_info'; // Ensure the table name is correct
    protected $fillable = [
        'image',
        'name_en',
        'name_ar',
        'phone',
        'email',
        'address_en',
        'address_ar',
        'location',
        'main_color',
        'sub_color',
        'text_color',
        'button_color',
        'icon_color',
        'text_filed_color',
        'hint_color',
        'button_text_color',
        'card_color',
        'label_color',
        'about_us_en',
        'about_us_ar',
        'mission_en',
        'mission_ar',
        'vision_en',
        'vision_ar',
    ];
    protected $casts = [
        'created_at' => 'datetime',

    ];

    public function getLogoImageAttribute($value)
    {
        return asset('storage/' . $value);
    }
    public function setLogoImageAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $this->attributes['logo_image'] = $value;
        } elseif ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->attributes['logo_image'] = $value->store('company_logos', 'public');
        } else {
            $this->attributes['logo_image'] = null;
        }
    }
    public function getVisionArAttribute($value)
    {
        return $value ?: '';
    }
    public function getAboutUsAttribute()
    {
        return [
            'en' => $this->about_us_en,
            'ar' => $this->about_us_ar,
        ];
    }
    public function getVisionAttribute()
    {
        return [
            'en' => $this->vision_en,
            'ar' => $this->vision_ar,
        ];
    }
    public function  getAboutUsEnAttribute($value)
    {
        return $value ?: '';
    }
    public function getAboutUsArAttribute($value)
    {
        return $value ?: ''; }
    public function getMissingEnAttribute($value)
    {
        return $value ?: '';    }
    public function getMissingArAttribute($value)
    {
        return $value ?: '';        }
    public function getVisionEnAttribute($value)
    {
        return $value ?: '';


}
}
