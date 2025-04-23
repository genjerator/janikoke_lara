import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';

export default function Testmap(param) {

    console.log(param.map);
    return (
        <div className="h-screen flex flex-col">

            {/* Header */}
            <header className="bg-white shadow p-4 flex justify-between items-center">
                <h1 className="text-xl font-bold">Map App</h1>
                <div className="flex space-x-2">
                    <input
                        type="text"
                        placeholder="Search..."
                        className="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring focus:border-blue-300"
                    />
                    <button className="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600 transition">
                        Search
                    </button>
                </div>
            </header>

            {/* Map Section */}
            <main className="flex-1">

                {/*<div className="w-full h-full border-t border-gray-300" dangerouslySetInnerHTML={{__html: map}}/>*/}
            </main>

        </div>
    );
}
