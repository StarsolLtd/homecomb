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
            <Row className="w-100">
                <Col id="home" className="align-self-center text-center">
                    <Container>
                        <TextLogo />
                        <Form>
                            <FormGroup>
                                <Label for="propertySearch">Find a property</Label>
                                <PropertyAutocomplete
                                    inputId="propertySearch"
                                    source="/api/property/suggest-property"
                                    placeholder="Start typing an address..."
                                />
                            </FormGroup>
                            <p id="propertySearchHelp" className="text-muted">After you've entered a few characters, you
                                will see suggested results</p>
                        </Form>
                    </Container>
                </Col>
            </Row>
        );
    }
}

export default Home;
