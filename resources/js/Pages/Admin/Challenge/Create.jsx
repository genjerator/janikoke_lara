import React from 'react';
import AdminLayout from "@/Layouts/AdminLayout.jsx";
import {Inertia} from "@inertiajs/inertia";
import ChallengeForm from "@/Components/Challenge/ChallengeForm.jsx";

const Edit = ({round}) => {

    const challenge = {
        "name": "",
        "description": "",
        "active": true,
        "round_id": round.id
    }
    const handleSubmit = (data) => {
        // Handle form submission with formData
        console.log(data, "Submitted Data for Editing");
        Inertia.post(`/admin/challenge`, data, {
            onSuccess: () => {
                console.log("Challenge updated successfully");

            },
            onError: (errors) => {
                console.log("Error updating challenge:", errors);
            }
        });
    };
    return (
        <div>
            <h1>Create a new Challenge </h1>
            <div>
                <ChallengeForm challenge={challenge} onSubmit={handleSubmit}></ChallengeForm>
            </div>
        </div>
    );
};
Edit.layout = page => <AdminLayout header={"test"} children={page}/>
export default Edit;
