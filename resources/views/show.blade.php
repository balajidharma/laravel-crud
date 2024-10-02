<div class="w-full py-2">
    <div class="min-w-full border-base-200 shadow">
        <table class="table-fixed w-full text-sm">
            <tbody>
                @foreach ($fields as $field)
                @isset($item->display_values[$field['attribute']])
                <tr>
                    <td class="border-b border-slate-100 p-4 pl-8 text-slate-500">{{ $field['label'] ?? $field['attribute'] }}</td>
                    <td class="border-b border-slate-100 p-4 text-slate-500">{!! $item->display_values[$field['attribute']] !!}</td>
                </tr>
                @endisset
                @endforeach
            </tbody>
        </table>
    </div>
</div>
