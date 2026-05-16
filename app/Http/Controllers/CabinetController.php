<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    public function __invoke(Request $request): View
    {
        $master = $request->user();

        $masterClasses = $master
            ->taughtMasterClasses()
            ->with(['creativityType', 'participants'])
            ->withCount('enrollments')
            ->orderBy('class_date')
            ->orderBy('class_time')
            ->get();

        return view('pages.cabinet', [
            'types' => CreativityType::query()->orderBy('name')->get(),
            'master' => $master,
            'masterClasses' => $masterClasses,
            'bodyClass' => 'dp',
        ]);
    }
}
