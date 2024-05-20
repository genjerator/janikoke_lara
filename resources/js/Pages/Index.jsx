import React from 'react';
import { Inertia } from '@inertiajs/inertia';
import { InertiaLink } from '@inertiajs/inertia-react';

const Index = ({ rounds }) => {
    const handleDelete = (id) => {
        Inertia.delete(route('rounds.destroy', id));
    };

    return (
        <div>
            <h1>Rounds</h1>
            <InertiaLink href={route('rounds.create')}>Create Round</InertiaLink>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Starts At</th>
                    <th>Ends At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {rounds.map(round => (
                    <tr key={round.id}>
                        <td>{round.name}</td>
                        <td>{round.description}</td>
                        <td>{round.starts_at}</td>
                        <td>{round.ends_at}</td>
                        <td>
                            <InertiaLink href={route('rounds.edit', round.id)}>Edit</InertiaLink>
                            <button onClick={() => handleDelete(round.id)}>Delete</button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
};

export default Index;
