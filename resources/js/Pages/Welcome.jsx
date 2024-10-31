import React from "react";
import Guest from "@/Layouts/GuestLayout.jsx";
import AdminLayout from "@/Layouts/AdminLayout.jsx";

function Welcome() {
    return (
        <>
            <div
                className="relative sm:flex sm:justify-center sm:items-center bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                Андя пиє пивко...
            </div>
            <div
                className="relative sm:flex sm:justify-center sm:items-center bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <video
                    id="my-video"
                    className="video-js"
                    controls
                    preload="auto"
                    width="640"
                    height="264"
                    data-setup="{}"
                    poster="{{ config('app.url') }}/band.jpg"
                >
                    <source src="https://haligali.nullroute.stream/tunein"/>

                    <p className="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a
                        web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank"
                        >supports HTML5 video</a
                        >
                    </p>
                </video>
            </div>
        </>
    );
}

Welcome.layout = page => <Guest children={page}/>
export default Welcome;
