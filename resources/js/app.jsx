// import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { APIProvider } from '@vis.gl/react-google-maps'; // Import APIProvider

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <APIProvider apiKey="AIzaSyC56Te0QXLZVHcF76LVO3MNBOFnoSdVP98" libraries={['geometry']} onLoad={() => console.log('Maps API has loaded.')}>
                <App {...props} />
            </APIProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});
