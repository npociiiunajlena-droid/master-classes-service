<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $types = CreativityType::query()->orderBy('name')->get();

        return view('pages.home', [
            'types' => $types,
        ]);
    }
}
