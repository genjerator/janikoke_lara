<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = $this->getTotalStats();
            @endphp

            <!-- Total Scores Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Scores (30 days)</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_scores'] }}</p>
                    </div>
                    <div class="text-blue-500 text-4xl">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Amount Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Amount (30 days)</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_amount'], 0) }}</p>
                    </div>
                    <div class="text-green-500 text-4xl">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Users Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Users (30 days)</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="text-purple-500 text-4xl">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292m-7.08 2.474L9 13.5h6l3.08 3.5M9 9h6m4 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Areas Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Areas (30 days)</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_areas'] }}</p>
                    </div>
                    <div class="text-orange-500 text-4xl">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scores Per Day Per User Chart -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Scores Per Day - Each Active User (Last 30 Days)</h3>
            </div>
            <div class="p-6">
                <canvas id="userDailyChart" class="max-h-96"></canvas>
            </div>
        </div>

        <!-- Charts and Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scores Per Day Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Scores Per Day (Last 30 Days)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->getScoresPerDay() as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $item->date }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $item->count }}</td>
                                    <td class="px-6 py-3 text-sm font-medium text-green-600">{{ number_format($item->total_amount, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Scores Per User Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top 10 Users by Score (Last 30 Days)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->getScoresPerUser() as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $item->name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600">{{ $item->score_count }}</td>
                                    <td class="px-6 py-3 text-sm font-medium text-blue-600">{{ number_format($item->total_amount, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Active Areas with Challenges Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Active Areas & Challenges (Last 30 Days)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Challenge</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Score Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lastArea = null;
                        @endphp
                        @forelse($this->getAreasWithChallenges() as $item)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900 font-semibold">
                                    @if($item->id !== $lastArea)
                                        {{ $item->name }}
                                        @php $lastArea = $item->id; @endphp
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $item->challenge_name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $item->score_count }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-orange-600">{{ number_format($item->total_amount, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = {!! json_encode($this->getChartData()) !!};

                const ctx = document.getElementById('userDailyChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: chartData.datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                title: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Amount'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Date'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-filament-panels::page>
