import React from "react";
import { usePage, router } from "@inertiajs/react";
import Guest from "@/Layouts/GuestLayout.jsx";

function Welcome() {
    const googleAuthUrl = import.meta.env.VITE_GOOGLE_AUTH_URL || "notset";
    const { auth } = usePage().props;
    const googleUser = auth?.google_user;

    function handleLogout(e) {
        e.preventDefault();
        router.post(route('google.logout'));
    }

    return (
        <>
            <div className="flex flex-col items-center justify-start min-h-screen bg-gray-100 p-4">
                {/* Top text */}
                <div className="mb-4 text-white bg-gray-800 px-6 py-3 rounded-lg shadow-md">
                    ...Андя піє пивко...
                </div>

                {googleUser ? (
                    <div className="mb-4 flex items-center gap-3">
                        <span className="text-sm font-medium text-gray-700">
                            👋 {googleUser.name || googleUser.email}
                        </span>
                        <button
                            onClick={handleLogout}
                            className="inline-flex items-center justify-center rounded-lg bg-red-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-600"
                        >
                            Logout
                        </button>
                    </div>
                ) : (
                    <a
                        href={googleAuthUrl}
                        className="mb-4 inline-flex items-center justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50"
                    >
                        Sign in with Google
                    </a>
                )}

                {/* Video */}
                <div className="w-full max-w-4xl h-[80vh]">
                    <video
                        id="my-video"
                        className="video-js w-full h-full object-contain rounded-lg shadow-lg"
                        controls
                        autoPlay
                        preload="auto"
                        data-setup="{}"
                        poster="/band.jpg"
                    >
                        <source src="https://haligali.nullroute.stream" />
                        <p className="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that
                            <a
                                href="https://videojs.com/html5-video-support/"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                supports HTML5 video
                            </a>
                        </p>
                    </video>
                </div>
            </div>

        </>
    );
}

Welcome.layout = page => <Guest children={page}/>
export default Welcome;
