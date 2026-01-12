<x-app-layout>
    <main class="flex-1 p-6">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 id="pageHeading" class="text-2xl font-semibold text-gray-800">Edit Cash Flow Statement</h1>
                <p class="text-sm text-gray-500">Update your Cash Flow Statement and re-run calculations</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50"><i class="fas fa-arrow-left"></i><span class="text-sm">Back</span></a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <form action="{{ route('reports.financial.cashflow.update', $report->id) }}" method="POST" class="p-6 space-y-6 max-w-3xl">
                @csrf
                @method('PUT')

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <input id="description" type="text" name="description" value="{{ old('description', $report->description) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="max-w-xs">
                    <label for="method" class="block text-sm font-medium text-gray-700">Method</label>
                    <select id="method" name="method" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                        @php $m = old('method', $report->method); @endphp
                        <option value="indirect" {{ $m==='indirect'?'selected':'' }}>Indirect method</option>
                        <option value="direct" {{ $m==='direct'?'selected':'' }}>Direct method</option>
                    </select>
                </div>

                <div>
                    <div id="columnsContainer" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl">
                            <div class="sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">From</label>
                                <div class="relative mt-1">
                                    <input id="from" type="date" name="from" value="{{ old('from', optional($report->from ? \Carbon\Carbon::parse($report->from) : null)?->format('Y-m-d')) }}" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                    <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                                </div>
                            </div>
                            <div class="sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">To</label>
                                <div class="relative mt-1">
                                    <input id="to" type="date" name="to" value="{{ old('to', optional($report->to ? \Carbon\Carbon::parse($report->to) : null)?->format('Y-m-d')) }}" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                    <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                                </div>
                            </div>
                            <div class="sm:col-span-4">
                                <label for="column_label" class="block text-sm font-medium text-gray-700">Column name</label>
                                <input id="column_label" type="text" name="column_label" placeholder="Optional" value="{{ old('column_label', $report->column_label) }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <button id="addComparativeBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Add comparative column <i class="fas fa-caret-down"></i></button>
                    </div>
                </div>

                <div>
                    <label for="footer" class="block text-sm font-medium text-gray-700">Footer</label>
                    <textarea id="footer" name="footer" rows="6" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">{{ old('footer',$report->footer) }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-start">
                    <button id="submitBtn" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">Update</button>
                </div>
            </form>
        </div>
    </main>

    <script>
    (function(){
        const columnsContainer = document.getElementById('columnsContainer');
        const addBtn = document.getElementById('addComparativeBtn');
        const fromMain = document.getElementById('from');
        const toMain = document.getElementById('to');
        const submitBtn = document.getElementById('submitBtn');
        let compIndex = 0;
        function buildComparativeRow(index){
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl';
            row.innerHTML = `
                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-700">From</label>
                    <div class="relative mt-1">
                        <input type="date" name="comparatives[${index}][from]" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                        <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                    </div>
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-700">To</label>
                    <div class="relative mt-1">
                        <input type="date" name="comparatives[${index}][to]" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                        <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Column name</label>
                    <input type="text" name="comparatives[${index}][label]" placeholder="Optional" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                </div>
                <div class="sm:col-span-1 flex items-end">
                    <button type="button" class="remove-comp mt-1 inline-flex items-center justify-center h-10 w-10 border rounded-md text-gray-600 hover:bg-gray-50" title="Remove column"><i class="far fa-trash-alt"></i></button>
                </div>`;
            row.querySelector('.remove-comp').addEventListener('click', () => row.remove());
            return row;
        }

        addBtn?.addEventListener('click', () => { const row = buildComparativeRow(compIndex++); columnsContainer.appendChild(row); });

        function validate(){ let ok = true; const from = fromMain?.value ? new Date(fromMain.value) : null; const to = toMain?.value ? new Date(toMain.value) : null; if(from && to && from > to) ok = false; if(submitBtn){ submitBtn.disabled = !ok; submitBtn.classList.toggle('opacity-50', !ok); submitBtn.classList.toggle('cursor-not-allowed', !ok); } return ok; }
        fromMain?.addEventListener('change', validate); toMain?.addEventListener('change', validate); validate();
    })();
    </script>
</x-app-layout>