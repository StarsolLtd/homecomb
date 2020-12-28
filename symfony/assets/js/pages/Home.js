import React from 'react';
import PropertyAutocomplete from "../components/PropertyAutocomplete";
import TextLogo from "../components/TextLogo";
import {Container, Form, FormGroup, Label} from "reactstrap";
import '../../styles/home.scss';

class Home extends React.Component {
    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <div id="home" className="align-self-center text-center">
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
            </div>
        );
    }
}

export default Home;
