<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreativityType;
use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $masterClasses = $user
            ->enrolledMasterClasses()
            ->with(['creativityType', 'master'])
            ->whereDate('class_date', '>=', now()->toDateString())
            ->orderBy('class_date')
            ->orderBy('class_time')
            ->get();

        return view('pages.my-enrollments', [
            'types' => CreativityType::query()->orderBy('name')->get(),
            'masterClasses' => $masterClasses,
        ]);
    }

    public function confirm(Request $request, MasterClass $masterClass): View|RedirectResponse
    {
        $user = $request->user();
        $restriction = $this->resolveEnrollmentRestrictionMessage($user, $masterClass);

        if ($restriction !== null) {
            return redirect()
                ->route('categories.show', $masterClass->creativityType)
                ->with('error', $restriction);
        }

        return view('pages.enrollment-confirm', [
            'masterClass' => $masterClass->load(['master', 'creativityType']),
            'user' => $user,
            'types' => CreativityType::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MasterClass $masterClass): RedirectResponse
    {
        $user = $request->user();
        $restriction = $this->resolveEnrollmentRestrictionMessage($user, $masterClass);

        if ($restriction !== null) {
            return redirect()
                ->route('categories.show', $masterClass->creativityType)
                ->with('error', $restriction);
        }

        try {
            $restrictionInsideTransaction = DB::transaction(function () use ($user, $masterClass): ?string {
                $lockedMasterClass = MasterClass::query()
                    ->whereKey($masterClass->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                User::query()
                    ->whereKey($user->id)
                    ->lockForUpdate()
                    ->first();

                $restrictionMessage = $this->resolveEnrollmentRestrictionMessage($user, $lockedMasterClass);

                if ($restrictionMessage !== null) {
                    return $restrictionMessage;
                }

                Enrollment::query()->create([
                    'master_class_id' => $lockedMasterClass->id,
                    'user_id' => $user->id,
                ]);

                return null;
            }, 3);
        } catch (QueryException) {
            return redirect()
                ->route('categories.show', $masterClass->creativityType)
                ->with('error', 'Не удалось подтвердить запись. Попробуйте ещё раз.');
        }

        if ($restrictionInsideTransaction !== null) {
            return redirect()
                ->route('categories.show', $masterClass->creativityType)
                ->with('error', $restrictionInsideTransaction);
        }

        return redirect()
            ->route('categories.show', $masterClass->creativityType)
            ->with('status', 'Запись на мастер-класс подтверждена.');
    }

    public function cancel(MasterClass $masterClass): RedirectResponse
    {
        return redirect()
            ->route('categories.show', $masterClass->creativityType)
            ->with('status', 'Запись отменена.');
    }

    public function destroy(Request $request, MasterClass $masterClass): RedirectResponse
    {
        $deleted = Enrollment::query()
            ->where('master_class_id', $masterClass->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        if ($deleted === 0) {
            return redirect()
                ->route('enrollments.index')
                ->with('error', 'Запись не найдена или уже отменена.');
        }

        return redirect()
            ->route('enrollments.index')
            ->with('status', 'Запись успешно отменена.');
    }

    private function resolveEnrollmentRestrictionMessage(?User $user, MasterClass $masterClass): ?string
    {
        if (! $user) {
            return 'Для записи требуется авторизация.';
        }

        if (! $user->isVisitor()) {
            return 'Ведущий мастер-класса не может записываться как участник.';
        }

        $alreadyEnrolled = Enrollment::query()
            ->where('master_class_id', $masterClass->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyEnrolled) {
            return 'Вы уже записаны на этот мастер-класс.';
        }

        $sameDateTimeEnrolled = Enrollment::query()
            ->where('user_id', $user->id)
            ->whereHas('masterClass', function ($query) use ($masterClass): void {
                $query
                    ->whereDate('class_date', $masterClass->class_date->format('Y-m-d'))
                    ->whereTime('class_time', MasterClass::normalizeSlot($masterClass->class_time));
            })
            ->exists();

        if ($sameDateTimeEnrolled) {
            return 'У вас уже есть запись на это же время и дату.';
        }

        $participantsCount = Enrollment::query()
            ->where('master_class_id', $masterClass->id)
            ->count();

        if ($participantsCount >= $masterClass->max_participants) {
            return 'Свободных мест больше нет.';
        }

        return null;
    }
}
