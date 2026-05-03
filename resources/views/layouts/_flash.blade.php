@if (session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm flash-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm flash-error">
        {{ session('error') }}
    </div>
@endif
