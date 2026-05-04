<?php

namespace App\Helpers;

class CustomSettings
{
    /**
     * Return the company model (your existing implementation).
     * Replace with however you already fetch it, e.g.:
     *   return CompanyInfo::first();
     */
    private static function get()
    {
        return \App\Models\CompanyInfo::first();
    }

    /* ================================================================
       APP SETTINGS  — returned as a flat array used by master.blade
    ================================================================ */

    public static function appSettings(): array
    {
        $company = self::get();

        return [
            // ── Branding ──────────────────────────────────────────────
            'image'             => $company->image             ?? null,
            'name_en'           => $company->name_en           ?? 'Coffee Shop',
            'name_ar'           => $company->name_ar           ?? 'متجر القهوة',
            'phone'             => $company->phone             ?? null,

            // ── Colours ───────────────────────────────────────────────
            'main_color'        => $company->main_color        ?? '#6C63FF',
            'sub_color'         => $company->sub_color         ?? '#B621FE',
            'text_color'        => $company->text_color        ?? '#22223B',
            'button_color'      => $company->button_color      ?? '#4A4E69',
            'icon_color'        => $company->icon_color        ?? '#9A8C98',
            'text_filed_color'  => $company->text_filed_color  ?? '#F2E9E4',
            'card_color'        => $company->card_color        ?? '#F2E9E4',
            'button_text_color' => $company->button_text_color ?? '#C9ADA7',
            'hint_color'        => $company->hint_color        ?? '#F2E9E4',
            'label_color'       => $company->label_color       ?? '#4A4E69',

            // ── FIX: company location exposed so Blade & controllers can use it
            'country_id'        => $company->country_id        ?? null,
            'city_id'           => $company->city_id           ?? null,
        ];
    }

    /* ================================================================
       LOCATION HELPERS
       Used by OrderController::edit() and edit-order Blade
    ================================================================ */

    /**
     * Returns the company's country_id as a string (empty string if unset).
     */
    public static function companyCountryId(): string
    {
        return (string)(self::get()->country_id ?? '');
    }

    /**
     * Returns the company's city_id as a string (empty string if unset).
     */
    public static function companyCityId(): string
    {
        return (string)(self::get()->city_id ?? '');
    }
}