import React from 'react';
import {Anchor, Button} from "@zendeskgarden/react-buttons";
import { Body, Cell, Head, HeaderCell, HeaderRow, Row, Table } from '@zendeskgarden/react-tables';
import Authenticated from "@/Layouts/AuthenticatedLayout.jsx";
import AdminLayout from "@/Layouts/AdminLayout.jsx";
import {Inertia} from "@inertiajs/inertia";

const Edit = ({ round }) => {
    console.log(round);
    return (
        <div>
            <h1>Round {round.id}</h1>
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

                </Body>
            </Table>
        </div>
    );
};
Edit.layout = page => <AdminLayout header={"test"} children={page} />
export default Edit;
