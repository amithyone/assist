@if (session('status'))
    <div class="assist-alert assist-alert-success assist-admin-alert">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="assist-alert assist-alert-error assist-admin-alert">
        @foreach ($errors->all() as $err)
            <p>{{ $err }}</p>
        @endforeach
    </div>
@endif
