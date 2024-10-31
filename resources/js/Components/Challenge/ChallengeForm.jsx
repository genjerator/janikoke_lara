import React, {useState} from 'react';
import {
    Field,
    Label,
    Input,
    Textarea,
    Select,
    Checkbox, Hint, Message,
} from '@zendeskgarden/react-forms';
import {Row, Col} from '@zendeskgarden/react-grid';
import {Button} from "@zendeskgarden/react-buttons";
import {useForm} from "@inertiajs/react";

const ChallengeForm = ({ challenge = {}, onSubmit }) => {

    const {data, setData, post,put, errors, processing} = useForm({
        id: challenge.id,
        name: challenge.name,
        description: challenge.description,
        active: challenge.active,
        round_id: challenge.round_id,
        type: challenge.type,
    })
    console.log(data)
    console.log(errors.name)
    const handleFormSubmit = (e) => {
        e.preventDefault();
        onSubmit(data);
    };

    return (
        <form onSubmit={handleFormSubmit}>
            <Row>
                <Col size="auto">
                    <Field>
                        <Checkbox
                            name="active"
                            checked={data.active}
                            onChange={(e) => setData("active", e.target.value)}
                        >
                            <Label>Active {data.active ? "true" : "false"}</Label>
                        </Checkbox>
                    </Field>
                </Col>
            </Row>
            <Row>
                <Col size="auto">
                    <Field>
                        <Label>Name {data.name}</Label>
                        <Input
                            name="name"
                            value={data.name}
                            onChange={(e) => setData("name", e.target.value)}
                        />
                        {errors.name && <Message validation="warning">{errors.name}</Message>}
                    </Field>
                </Col>
            </Row>
            <Row>
                <Col size="auto">
                    <Field>
                        <Label>Description</Label>
                        <Textarea
                            name="description"
                            value={data.description}
                            onChange={(e) => setData("description", e.target.value)}
                        />
                        {errors.description && <Message validation="warning">{errors.description}</Message>}
                    </Field>
                </Col>
            </Row>

            <Row>
                <Col size="auto">
                    <Field>
                        <Label>Type</Label>
                        <Select
                            name="type"
                            value={data.type}
                            onChange={(e) => setData("type", e.target.value)}
                        >
                            <option value="zigzag">Zigzag</option>
                            <option value="ten_each">Teneach</option>
                            {/* Add more options if needed */}
                        </Select>
                    </Field>
                </Col>
            </Row>
            <Row>
                <Col size="auto">
                    <Button type="submit">Submit</Button>
                </Col>
            </Row>
        </form>
    );
};

export default ChallengeForm;
