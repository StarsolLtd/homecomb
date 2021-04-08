import React from 'react';
import { Col, Container, Row } from 'reactstrap';
import Constants from "../Constants";

class Contact extends React.Component {

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Contact Us';
    }

    render() {
        return (
            <Container>
                <Row>
                    <Col md="12">
                        <h1>Contact Us</h1>
                        <p>
                            Coming soon.
                        </p>
                    </Col>
                </Row>
            </Container>
        );
    }
}

export default Contact;