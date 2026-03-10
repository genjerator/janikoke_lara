import React from "react";
import { router } from "@inertiajs/react";
import Guest from "@/Layouts/GuestLayout.jsx";

function GoogleLoginFailed({ error }) {
    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
            <div className="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
                {/* Error icon */}
                <div className="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-red-100">
                    <svg className="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>

                <h1 className="text-2xl font-bold text-gray-800 mb-2">Login Failed</h1>

                <p className="text-gray-500 mb-4">Something went wrong during Google login.</p>

                {error && (
                    <div className="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-6 text-sm text-red-700 text-left">
                        <span className="font-semibold">Error: </span>{error}
                    </div>
                )}

                <button
                    onClick={() => router.visit(route('home'))}
                    className="w-full rounded-lg bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 transition"
                >
                    Try Again
                </button>
            </div>
        </div>
    );
}

GoogleLoginFailed.layout = page => <Guest children={page} />;
export default GoogleLoginFailed;

