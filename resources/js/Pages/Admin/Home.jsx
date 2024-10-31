
import React from 'react';
import AdminLayout from "@/Layouts/AdminLayout.jsx";

const Home = () => {
    return (
        <div>
            <h1>Hello, Inertia.js with React!</h1>
        </div>
    );
};
Home.layout = page => <AdminLayout header={"dashboard"} children={page} />
export default Home;
