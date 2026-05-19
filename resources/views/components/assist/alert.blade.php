@if ($errors->any())
    <div class="assist-alert assist-alert-error">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status') && ! request()->routeIs('assist.home'))
    <div class="assist-alert assist-alert-success">{{ session('status') }}</div>
@endif
