<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            General Ledger Summary Reports
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-semibold">General Ledger Summary Reports</h1>
                <a href="{{ route('reports.general-ledger.summary') }}"
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">From</th>
                            <th class="px-4 py-2 text-left">To</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $r)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $r->description }}</td>
                                <td class="px-4 py-2">{{ optional($r->from_date)->toDateString() }}</td>
                                <td class="px-4 py-2">{{ optional($r->to_date)->toDateString() }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-blue-600 hover:underline"
                                       href="{{ route('reports.general-ledger.summary.show', $r) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    No reports yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
