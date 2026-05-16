@if(isset($types) && $types->isNotEmpty())
    <ul class="menu">
        @auth
            @if(auth()->user()->isVisitor())
                <li>
                    <a href="{{ route('enrollments.index') }}" @if(request()->routeIs('enrollments.index') || request()->routeIs('enrollments.destroy')) style="background:#2b2586;" @endif>
                        Мои записи
                    </a>
                </li>
            @endif
        @endauth
        @foreach($types as $type)
            <li>
                <a href="{{ route('categories.show', $type) }}" @if(isset($currentType) && $currentType?->id === $type->id) style="background:#2b2586;" @endif>
                    {{ $type->name }}
                </a>
            </li>
        @endforeach
    </ul>
@endif
