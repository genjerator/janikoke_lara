import React from 'react';
import AdminLayout from "@/Layouts/AdminLayout.jsx";
import {Inertia} from "@inertiajs/inertia";
import ChallengeForm from "@/Components/Challenge/ChallengeForm.jsx";


const Edit = ({challenge}) => {

    const handleSubmit = (data) => {
        // Handle form submission with formData
        console.log(data, "Submitted Data for Editing");
        Inertia.put(`/admin/challenge/${data.id}`, data, {
            onSuccess: () => {
                console.log("Challenge updated successfully");
                // Optionally, show a success message or navigate away
            },
            onError: (errors) => {
                console.log("Error updating challenge:", errors);
                // Optionally, handle errors and display them in the form
            }
        });
    };
    return (
        <>
            <h1>Challenge {challenge.name}</h1>

                <ChallengeForm challenge={challenge} onSubmit={handleSubmit}></ChallengeForm>

        </>
    );
};
Edit.layout = page => <AdminLayout header={"test"} children={page}/>
export default Edit;
