import React from 'react';
import { Col, Container, Row } from 'reactstrap';

import Constants from "../Constants";

class PrivacyPolicy extends React.Component {

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Terms and Conditions';
    }

    render() {
        return (
            <Container>
                <Row>
                    <Col md="12">
                        <h1>Terms and Conditions for {Constants.SITE_NAME}</h1>

                        <p>To do.</p>
                    </Col>
                </Row>
            </Container>
        );
    }
}

export default PrivacyPolicy;