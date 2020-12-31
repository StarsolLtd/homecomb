import React from 'react';
import PropertyAutocomplete from "../components/PropertyAutocomplete";
import TextLogo from "../components/TextLogo";
import {Col, Container, Form, FormGroup, Label, Row} from "reactstrap";
import '../../styles/home.scss';

class Home extends React.Component {
    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <Row className="w-100" id="home-background">
                <Col id="home" className="align-self-center text-center">
                    <Container className="rounded-lg bg-light-translucent-90 p-5">
                        <TextLogo />
                        <Form>
                            <FormGroup>
                                <Label for="propertySearch">Find tenant reviews for properties and lettings agents</Label>
                                <PropertyAutocomplete
                                    inputId="propertySearch"
                                    source="/api/property/suggest-property"
                                    placeholder="Start typing an address... e.g. 249 Victoria Road"
                                />
                            </FormGroup>
                            <p>
                                After you've entered a few characters, you will see suggested results
                            </p>
                        </Form>
                    </Container>
                </Col>
            </Row>
        );
    }
}

export default Home;
