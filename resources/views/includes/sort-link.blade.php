@php
    $down_fill = 'lightgray';
    $up_fill = 'lightgray';
    $attribute = $attribute ?? '';
    $identifier = $identifier ?? '';
    $label = $label ?? '';
    if(request()->query($identifier.'sort') == $attribute) {
        $up_fill = 'black';
    }
    if(request()->query($identifier.'sort') == '-'.$attribute) {
        $down_fill = 'black';
    }
@endphp
<div class="flex items-center gap-2">
    <div class="d-flex flex-column">
    <svg class="d-inline-block" xmlns="http://www.w3.org/2000/svg" width="12px" height="12px" viewBox="0 0 15 15" fill="none">
        <path d="M7.5 3L15 11H0L7.5 3Z" fill="{{$up_fill}}"/>
    </svg>
    <svg class="d-inline-block" xmlns="http://www.w3.org/2000/svg" width="12px" height="12px" viewBox="0 0 15 15" fill="none">
        <path d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z" fill="{{$down_fill}}"/>
    </svg>
    </div>
    <div class="px-1">
    @if (request()->query($identifier.'sort') == $attribute)
        <a href="{{request()->fullUrlWithQuery([$identifier.'sort' => '-'.$attribute])}}" class="text-decoration-none text-primary">{{ __($label) }}</a>
    @else
        <a href="{{request()->fullUrlWithQuery([$identifier.'sort' => $attribute])}}" class="text-decoration-none text-primary">{{ __($label) }}</a>
    @endif
    </div>
</div>