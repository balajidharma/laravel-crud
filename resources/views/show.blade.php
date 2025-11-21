<div class="w-full py-2">
    <div class="overflow-x-auto">
        @canany(['adminUpdate', 'adminDelete'], $item)
            <div class="flex justify-end gap-2 mt-2">
                <form action="{{ $routes['destroy']($item->id) }}" method="POST">
                    <div>
                        @isset ($routes['edit'])
                            @can('adminUpdate', $item)
                                <a href="{{$routes['edit']($item->id)}}" class="btn btn-primary">Edit</a>
                            @endcan
                        @endisset

                        @isset ($routes['destroy'])
                            @can('adminDelete', $item)
                                @csrf
                                @method('DELETE')
                                @if ($redirectUrl)
                                    <input type="hidden" name="_redirect" value="{{ $redirectUrl }}">
                                @endif
                                <button class="btn btn-error"
                                    onclick="return confirm('{{ __('Are you sure you want to delete?') }}')">
                                    Delete
                                </button>
                            @endcan
                        @endisset
                    </div>
                </form>
            </div>
        @endcanany
        <table class="table-auto w-full text-sm table-zebra">
            <tbody>
                @foreach ($fields as $field)
                @isset($item->display_values[$field['attribute']])
                <tr>
                    <td class="border-b border-slate-100 p-4 text-slate-500">{{ $field['label'] ?? $field['attribute'] }}</td>
                    <td class="border-b border-slate-100 p-4 text-slate-500 break-all">{!! $item->display_values[$field['attribute']] !!}</td>
                </tr>
                @endisset
                @endforeach
            </tbody>
        </table>
    </div>
</div>
