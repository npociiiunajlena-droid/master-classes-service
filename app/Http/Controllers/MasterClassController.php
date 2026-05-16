<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterClassRequest;
use App\Http\Requests\UpdateMasterClassRequest;
use App\Models\CreativityType;
use App\Models\MasterClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MasterClassController extends Controller
{
    public function create(Request $request): View
    {
        $busySlotsByDate = $request->user()
            ->taughtMasterClasses()
            ->select(['class_date', 'class_time'])
            ->whereDate('class_date', '>=', now()->toDateString())
            ->get()
            ->groupBy(fn (MasterClass $masterClass, int $index): string => $masterClass->class_date->format('Y-m-d'))
            ->map(
                fn (Collection $dayClasses): array => $dayClasses
                    ->map(fn (MasterClass $masterClass, int $index): string => substr(MasterClass::normalizeSlot($masterClass->class_time), 0, 5))
                    ->values()
                    ->all()
            )
            ->all();

        return view('pages.master-class-create', [
            'types' => CreativityType::query()->orderBy('name')->get(),
            'slots' => MasterClass::AVAILABLE_SLOTS,
            'busySlotsByDate' => $busySlotsByDate,
        ]);
    }

    public function store(StoreMasterClassRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['master_id'] = $request->user()->id;
        $data['class_time'] = MasterClass::normalizeSlot($data['class_time']);

        MasterClass::query()->create($data);

        return redirect()
            ->route('cabinet.index')
            ->with('status', 'Мастер-класс добавлен в расписание.');
    }

    public function edit(Request $request, MasterClass $masterClass): View
    {
        $this->assertOwnedByCurrentMaster($request, $masterClass);

        return view('pages.master-class-edit', [
            'masterClass' => $masterClass->load('creativityType'),
            'types' => CreativityType::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMasterClassRequest $request, MasterClass $masterClass): RedirectResponse
    {
        $this->assertOwnedByCurrentMaster($request, $masterClass);

        $masterClass->update($request->validated());

        return redirect()
            ->route('cabinet.index')
            ->with('status', 'Мастер-класс обновлён.');
    }

    private function assertOwnedByCurrentMaster(Request $request, MasterClass $masterClass): void
    {
        if ($masterClass->master_id !== $request->user()->id) {
            abort(403, 'Можно редактировать только свои мастер-классы.');
        }
    }
}
