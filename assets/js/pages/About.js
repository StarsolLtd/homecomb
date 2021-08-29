import React from 'react';
import { Col, Container, Row } from 'reactstrap';
import Constants from "../Constants";

export default class About extends React.Component {

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | About';
    }

    render() {
        return (
            <Container>
                <Row>
                    <Col md="12">
                        <h1>About HomeComb</h1>
                        <p>
                            HomeComb is a letting agents review site.
                        </p>
                    </Col>
                </Row>
            </Container>
        );
    }
}
