<?php

namespace App\Filament\Resources\IvaTestResultResource\Pages;

use App\Filament\Resources\IvaTestResultResource;
use Filament\Resources\Pages\Page;

class IvaTestStatistics extends Page
{
    protected static string $resource = IvaTestResultResource::class;

    protected static string $view = 'filament.resources.iva-test-result-resource.pages.iva-test-statistics';
}
