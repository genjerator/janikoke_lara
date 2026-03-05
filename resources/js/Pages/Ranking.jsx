import React from 'react';
import Guest from '@/Layouts/GuestLayout.jsx';
import { Link } from '@inertiajs/react';

export default function Ranking({ rankings, period }) {
    const sortedRankings = [...rankings].sort((a, b) => {
        return b.total_amount - a.total_amount;
    });

    const activeUsers = sortedRankings.filter(user => user.score_count > 0);

    const getPeriodLabel = () => {
        return period === 'last30days' ? 'Last 30 Days' : 'All Time';
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
            <div className="container mx-auto px-4 py-8 sm:py-12 lg:py-16">
                {/* Header */}
                <div className="text-center mb-8 sm:mb-12">
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-indigo-600 mb-2 sm:mb-4">
                        🏆 User Rankings
                    </h1>
                    <p className="text-gray-600 text-base sm:text-lg lg:text-xl">
                        Leaderboard - {getPeriodLabel()}
                    </p>
                </div>

                {/* Period Toggle */}
                <div className="flex justify-center gap-4 mb-8">
                    <Link
                        href={route('ranking.index')}
                        className={`px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition ${
                            !period
                                ? 'bg-indigo-600 text-white shadow-lg'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                        }`}
                    >
                        All Time
                    </Link>
                    <Link
                        href={route('ranking.last30days')}
                        className={`px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition ${
                            period === 'last30days'
                                ? 'bg-indigo-600 text-white shadow-lg'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                        }`}
                    >
                        Last 30 Days
                    </Link>
                </div>

                {/* Rankings Table */}
                <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                    {sortedRankings.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                                        <th className="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold">
                                            Rank
                                        </th>
                                        <th className="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs sm:text-sm font-semibold">
                                            User
                                        </th>
                                        <th className="px-4 sm:px-6 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold">
                                            Score Count
                                        </th>
                                        <th className="px-4 sm:px-6 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold">
                                            Total Amount
                                        </th>
                                        <th className="px-4 sm:px-6 py-3 sm:py-4 text-center text-xs sm:text-sm font-semibold">
                                            Avg Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {sortedRankings.map((user, index) => (
                                        <tr
                                            key={user.id}
                                            className={`transition hover:bg-indigo-50 ${
                                                index === 0
                                                    ? 'bg-yellow-50 border-l-4 border-yellow-400'
                                                    : index === 1
                                                    ? 'bg-gray-50 border-l-4 border-gray-400'
                                                    : index === 2
                                                    ? 'bg-orange-50 border-l-4 border-orange-400'
                                                    : ''
                                            }`}
                                        >
                                            <td className="px-4 sm:px-6 py-3 sm:py-4">
                                                <div className="flex items-center justify-center">
                                                    {index === 0 && (
                                                        <span className="text-2xl sm:text-3xl">🥇</span>
                                                    )}
                                                    {index === 1 && (
                                                        <span className="text-2xl sm:text-3xl">🥈</span>
                                                    )}
                                                    {index === 2 && (
                                                        <span className="text-2xl sm:text-3xl">🥉</span>
                                                    )}
                                                    {index > 2 && (
                                                        <span className="text-sm sm:text-base font-bold text-gray-700">
                                                            #{user.rank}
                                                        </span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-4 sm:px-6 py-3 sm:py-4">
                                                <div>
                                                    <p className="font-semibold text-gray-900 text-sm sm:text-base">
                                                        {user.name}
                                                    </p>
                                                    <p className="text-gray-500 text-xs sm:text-sm">
                                                        {user.email}
                                                    </p>
                                                </div>
                                            </td>
                                            <td className="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                                <span className="inline-block bg-blue-100 text-blue-800 text-xs sm:text-sm font-semibold px-2 sm:px-3 py-1 rounded-full">
                                                    {user.score_count}
                                                </span>
                                            </td>
                                            <td className="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                                <span className="inline-block bg-green-100 text-green-800 text-xs sm:text-sm font-semibold px-2 sm:px-3 py-1 rounded-full">
                                                    {Number(user.total_amount).toLocaleString()}
                                                </span>
                                            </td>
                                            <td className="px-4 sm:px-6 py-3 sm:py-4 text-center text-sm sm:text-base text-gray-700">
                                                {Number(user.avg_amount).toFixed(2)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <p className="text-gray-500 text-lg">No rankings available yet.</p>
                        </div>
                    )}
                </div>

                {/* Stats Summary */}
                {sortedRankings.length > 0 && (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                        <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                            <p className="text-gray-600 text-sm font-medium">Total Users (Active)</p>
                            <p className="text-3xl font-bold text-gray-900 mt-2">
                                {activeUsers.length}
                            </p>
                        </div>
                        <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                            <p className="text-gray-600 text-sm font-medium">Total Scores</p>
                            <p className="text-3xl font-bold text-gray-900 mt-2">
                                {sortedRankings.reduce((sum, user) => sum + user.score_count, 0)}
                            </p>
                        </div>
                        <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                            <p className="text-gray-600 text-sm font-medium">Total Amount</p>
                            <p className="text-3xl font-bold text-gray-900 mt-2">
                                {Number(
                                    sortedRankings.reduce((sum, user) => sum + user.total_amount, 0)
                                ).toLocaleString()}
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

Ranking.layout = (page) => <Guest children={page} />;

