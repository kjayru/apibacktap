<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Concerns\CreatesAndReturnsToList;
use App\Filament\Resources\Coupons\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    use CreatesAndReturnsToList;

    protected static string $resource = CouponResource::class;
}
