import React from "react";
import Guest from "@/Layouts/GuestLayout.jsx";
import AdminLayout from "@/Layouts/AdminLayout.jsx";

function Welcome() {
    return (
        <>
            <div className="flex flex-col items-center justify-start min-h-screen bg-gray-100 p-4">
                {/* Top text */}
                <div className="mb-4 text-white bg-gray-800 px-6 py-3 rounded-lg shadow-md">
                    ...Андя піє пивко...
                </div>

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
