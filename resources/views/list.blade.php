@can('adminCreate', $model)
<div class="flex flex-row-reverse d-print-none with-border">
    <a href="{{  $routes['create'] }}" class="btn btn-primary">{{ __('Add') }}</a>
</div>
@endcan

<div class="py-2">
    <div class="min-w-full  border-base-200 shadow overflow-x-auto">
        <form method="GET" action="{{ $routes['index'] }}">
            <div class="py-2 flex flex-row-reverse">
                <div class="flex">
                    <input type="search" name="{{$identifier}}search" value="{{ request()->input($identifier.'search') }}" class="input input-bordered w-full max-w-xs" placeholder="{{ __('Search') }}">
                    <button type='submit' class='ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                        {{ __('Search') }}
                    </button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr class="bg-base-200">
                    @foreach ($fields as $field)
                    @if ($field['list'] ?? true)
                    <th class="py-2 px-4 bg-base-50 font-bold uppercase text-sm text-left">
                        @if ($field['sortable'])
                        @include('crud::includes.sort-link', ['label' => $field['label'] ?? $field['attribute'], 'attribute' => $field['attribute']])
                        @else
                        {{ $field['label'] ?? $field['attribute'] }}
                        @endif
                    </th>
                    @endif
                    @endforeach
                    @canany(['adminUpdate', 'adminDelete'], $model)
                    <th class="py-2 px-4 bg-base-50 font-bold uppercase text-sm text-left">
                        {{ __('Actions') }}
                    </th>
                    @endcanany
                </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    @foreach ($fields as $field)
                    @isset($item->display_values[$field['attribute']])
                    <td class="p-4">
                        {!! $item->display_values[$field['attribute']] !!}
                    </td>
                    @endisset
                    @endforeach
                    @canany(['adminUpdate', 'adminDelete'], $item)
                    <td class="p-4">
                        <form action="{{ $routes['destroy']($item->id) }}" method="POST">
                            <div>
                                @can('adminUpdate', $item)
                                <a href="{{$routes['edit']($item->id)}}" class="btn btn-square btn-ghost">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                @endcan

                                @can('adminDelete', $item)
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-square btn-ghost" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                                    </svg>
                                </button>
                                @endcan
                            </div>
                        </form>
                    </td>
                    @endcanany
                </tr>
                @endforeach
                @if($items->isEmpty())
                    <tr>
                        <td colspan="2">
                            <div class="flex flex-col justify-center items-center py-4 text-lg">
                                {{ __('No Result Found') }}
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="py-8">
        {{ $items->appends(request()->query())->links() }}
    </div>
</div>