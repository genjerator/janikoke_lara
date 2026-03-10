import React from "react";
import { usePage, router } from "@inertiajs/react";
import Guest from "@/Layouts/GuestLayout.jsx";

function GoogleLoginSuccess() {
    const { auth } = usePage().props;
    const googleUser = auth?.google_user;

    return (
        <div className="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4">
            <div className="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
                {/* Success icon */}
                <div className="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-green-100">
                    <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 className="text-2xl font-bold text-gray-800 mb-2">Login Successful!</h1>

                <p className="text-gray-500 mb-1">You have successfully logged in as</p>
                <p className="text-lg font-semibold text-indigo-600 mb-6">
                    {googleUser?.email ?? "—"}
                </p>

                {googleUser?.name && (
                    <p className="text-sm text-gray-400 mb-6">Welcome, {googleUser.name} 👋</p>
                )}

                <button
                    onClick={() => router.visit(route('home'))}
                    className="w-full rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition"
                >
                    Go to Home
                </button>
            </div>
        </div>
    );
}

GoogleLoginSuccess.layout = page => <Guest children={page} />;
export default GoogleLoginSuccess;

