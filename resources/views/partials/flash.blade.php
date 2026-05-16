@if(session('status') || session('error') || $errors->any())
    <div class="flash-wrapper">
        @if(session('status'))
            <div class="flash-item">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="flash-item flash-item--error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="flash-item flash-item--error">{{ $error }}</div>
            @endforeach
        @endif
    </div>
@endif
