<x-app-layout>
    <!-- Header Slot -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trial Balance Reports
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Controls -->
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-semibold">Trial Balance Reports</h1>

                <a href="{{ route('reports.general-ledger.trial-balance') }}"
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Create
                </a>
            </div>

            <!-- Table Wrapper -->
            <div class="bg-white rounded shadow-sm border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left">Title</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reports as $r)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $r->title ?? 'Trial Balance' }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a class="text-blue-600 hover:underline"
                                        href="{{ route('reports.general-ledger.trial-balance.show', $r) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
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
