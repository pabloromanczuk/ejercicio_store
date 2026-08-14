<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use Illuminate\Database\Seeder;

class HomeBannerSeeder extends Seeder
{
    private const BANNERS = [
        [
            'image' => 'banner-1.svg',
            'title' => 'Bienvenido a la Tienda',
            'subtitle' => 'Fruta y verdura fresca, directo del productor',
            'link' => null,
            'sort_order' => 1,
        ],
        [
            'image' => 'banner-2.svg',
            'title' => 'Ofertas de la semana',
            'subtitle' => 'Descuentos en frutas seleccionadas',
            'link' => null,
            'sort_order' => 2,
        ],
        [
            'image' => 'banner-3.svg',
            'title' => 'Pedí online',
            'subtitle' => 'Retirá en el local o recibilo en casa',
            'link' => null,
            'sort_order' => 3,
        ],
    ];

    public function run(): void
    {
        foreach (self::BANNERS as $banner) {
            HomeBanner::updateOrCreate(
                ['image' => $banner['image']],
                [
                    'title' => $banner['title'],
                    'subtitle' => $banner['subtitle'],
                    'link' => $banner['link'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
