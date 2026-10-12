@if(session('success'))
    <div class="alert alert-success mb-5" role="status">
        @include('partials.icon', ['name' => 'check', 'class' => 'size-5 mt-px'])
        <p>{{ session('success') }}</p>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-error mb-5" role="alert">
        @include('partials.icon', ['name' => 'info', 'class' => 'size-5 mt-px'])
        <div>
            @isset($errorTitle)<p class="font-semibold">{{ $errorTitle }}</p>@endisset
            @if($errors->count() === 1 && ! isset($errorTitle))
                <p>{{ $errors->first() }}</p>
            @else
                <ul class="mt-0.5 list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @endif
        </div>
    </div>
@endif
