@if ($displayFilters)
<div class="collapse collapse-arrow bg-base-200 mb-4" tabindex="0">
    <input type="checkbox" class="peer" checked/>
    <div class="collapse-title text-lg font-medium">
        Filters
    </div>
    <div class="collapse-content"> 
        <form method="GET" action="{{ url()->current() }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                @foreach($fields as $field)
                    @if (isset($field['filter']) && $field['filter'] !== 'between')
                        <div class="form-control w-full">
                            <label class="label" for="filter-{{ $identifier . $field['attribute'] }}">
                                <span class="label-text">{{ $field['label'] }}</span>
                            </label>
                            @if ($field['type'] == 'select' && isset($field['filter_options']))
                                <select class="select select-bordered w-full" 
                                    id="filter-{{ $identifier . $field['attribute'] }}"
                                    name="{{ $identifier . $field['attribute'] }}"
                                    @if (isset($field['attributes']))
                                        @foreach($field['attributes'] as $attributeKey => $attributeValue)
                                            {{ $attributeKey }}="{{ $attributeValue }}"
                                        @endforeach
                                    @endif
                                >
                                    <option value="">Select {{ $field['label'] }}</option>
                                    @foreach($field['filter_options'] as $optionKey => $optionName)
                                        <option value="{{ $optionKey }}" {{ (request()->input($identifier . $field['attribute']) !== null && request()->input($identifier . $field['attribute']) == $optionKey) ? 'selected' : '' }}>
                                            {!! $optionName !!}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif ($field['type'] == 'checkbox')
                                <select class="select select-bordered w-full"
                                    id="filter-{{ $identifier . $field['attribute'] }}"
                                    name="{{ $identifier . $field['attribute'] }}"
                                    @if (isset($field['attributes']))
                                        @foreach($field['attributes'] as $attributeKey => $attributeValue)
                                            {{ $attributeKey }}="{{ $attributeValue }}"
                                        @endforeach
                                    @endif
                                >
                                    <option value="">Any</option>
                                    <option value="1" {{ request()->input($identifier . $field['attribute']) == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ request()->input($identifier . $field['attribute']) == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            @else
                                <input type="{{$field['type']}}" 
                                    class="input input-bordered w-full"
                                    id="filter-{{ $identifier . $field['attribute'] }}"
                                    name="{{ $identifier . $field['attribute'] }}"
                                    value="{{ request()->input($identifier . $field['attribute']) }}"
                                    @if (isset($field['attributes']))
                                        @foreach($field['attributes'] as $attributeKey => $attributeValue)
                                            {{ $attributeKey }}="{{ $attributeValue }}"
                                        @endforeach
                                    @endif
                                >
                            @endif
                        </div>
                    @elseif (isset($field['filter']) && $field['filter'] == 'between')
                        <div class="form-control w-full">
                            <label class="label" for="filter-{{ $identifier . $field['attribute'] }}_start">
                                <span class="label-text">{{ $field['label'] }} Start</span>
                            </label>
                            <input type="date" 
                                class="input input-bordered w-full"
                                id="filter-{{ $identifier . $field['attribute'] }}_start"
                                name="{{ $identifier . $field['attribute'] }}_start"
                                value="{{ request()->input($identifier . $field['attribute'] . '_start') }}">

                            <label class="label" for="filter-{{ $identifier . $field['attribute'] }}_end">
                                <span class="label-text">{{ $field['label'] }} End</span>
                            </label>
                            <input type="date" 
                                class="input input-bordered w-full"
                                id="filter-{{ $identifier . $field['attribute'] }}_end"
                                name="{{ $identifier . $field['attribute'] }}_end"
                                value="{{ request()->input($identifier . $field['attribute'] . '_end') }}">
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="flex justify-end gap-2 mt-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <button type="reset" 
                    class="btn btn-secondary"
                    onclick="window.location='{{ url()->current() }}'">
                    Clear
                </button>
            </div>
        </form>
    </div>
</div>
@endif