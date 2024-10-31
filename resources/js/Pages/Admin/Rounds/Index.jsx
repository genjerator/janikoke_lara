import React from 'react';
import {Button} from "@zendeskgarden/react-buttons";
import {Body, Head, HeaderCell, HeaderRow, Table} from '@zendeskgarden/react-tables';
import AdminLayout from "@/Layouts/AdminLayout.jsx";
import {Inertia} from "@inertiajs/inertia";

const Index = ({rounds}) => {

    return (
        <div>
            <h1>Rounds</h1>
            <Table>
                <Head>
                    <HeaderRow>
                        <HeaderCell>Name</HeaderCell>
                        <HeaderCell>Description</HeaderCell>
                        <HeaderCell>Starts At</HeaderCell>
                        <HeaderCell>Ends At</HeaderCell>
                        <HeaderCell>Challenges</HeaderCell>
                    </HeaderRow>
                </Head>
                <Body>
                    {rounds.map(round => (
                        <HeaderRow>
                            <HeaderCell>{round.name}</HeaderCell>
                            <HeaderCell>{round.description}</HeaderCell>
                            <HeaderCell>{round.starts_at}</HeaderCell>
                            <HeaderCell>{round.ends_at}</HeaderCell>
                            <HeaderCell>{round.challenges.map(challenge => {

                                return (
                                    <div key={challenge.id}>

                                        {challenge.name}
                                        <Button onClick={() => Inertia.visit(route('admin.challenge.edit', {
                                            challengeId: challenge.id
                                        }))}>Edit
                                        </Button>
                                        <Button
                                            isDanger
                                            onClick={() => {
                                                if (window.confirm(`Are you sure you want to delete ${challenge.name}?`)) {
                                                    // Call your delete function here
                                                    Inertia.delete(route("admin.challenge.delete", {challengeId: challenge.id}));
                                                }
                                            }}
                                        >
                                            Delete
                                        </Button>
                                    </div>

                                )
                            })}<Button isPrimary
                                       onClick={() => Inertia.visit(route('admin.challenge.create', {"round": round.id}))}>Create
                            </Button>
                            </HeaderCell>
                        </HeaderRow>
                    ))}
                </Body>
            </Table>
        </div>
    );
};
Index.layout = page => <AdminLayout header={"test"} children={page}/>
export default Index;
