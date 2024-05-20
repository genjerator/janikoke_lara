import React from 'react';
import {Link} from "@inertiajs/react";
import {Anchor, Button} from "@zendeskgarden/react-buttons";
import { Body, Cell, Head, HeaderCell, HeaderRow, Row, Table } from '@zendeskgarden/react-tables';

const Index = ({ rounds }) => {

    return (
        <div>
            <h1>Roundsss</h1>
            <Table>
                <Head>
                <HeaderRow>
                    <HeaderCell>Name</HeaderCell>
                    <HeaderCell>Description</HeaderCell>
                    <HeaderCell>Starts At</HeaderCell>
                    <HeaderCell>Ends At</HeaderCell>
                    <HeaderCell>Actions</HeaderCell>
                </HeaderRow>
                </Head>
                <Body>
                {rounds.map(round => (
                    <HeaderRow key={round.id}>
                        <HeaderCell>{round.name}</HeaderCell>
                        <HeaderCell>{round.description}</HeaderCell>
                        <HeaderCell>{round.starts_at}</HeaderCell>
                        <HeaderCell>{round.ends_at}</HeaderCell>
                        <HeaderCell>
                            <Anchor href="/admin/rounds/">Edit</Anchor>
                            <Button onClick={() => handleDelete(round.id)}>Delete</Button>
                        </HeaderCell>
                    </HeaderRow>
                ))}
                </Body>
            </Table>
        </div>
    );
};

export default Index;
