<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __invoke(Request $request, CreativityType $creativityType): View
    {
        $types = CreativityType::query()->orderBy('name')->get();

        $masterClasses = $creativityType->masterClasses()
            ->with(['master', 'creativityType'])
            ->withCount('enrollments')
            ->whereDate('class_date', '>=', now()->toDateString())
            ->orderBy('class_date')
            ->orderBy('class_time')
            ->get();

        $userEnrollmentIds = [];

        if ($request->user()) {
            $userEnrollmentIds = $request->user()
                ->enrollments()
                ->pluck('master_class_id')
                ->all();
        }

        return view('pages.category', [
            'types' => $types,
            'currentType' => $creativityType,
            'masterClasses' => $masterClasses,
            'userEnrollmentIds' => $userEnrollmentIds,
        ]);
    }
}
