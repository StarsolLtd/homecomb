import React from 'react';
import ReactDOM from 'react-dom';
import { Col, Container, Row } from 'reactstrap';

class About extends React.Component {
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

ReactDOM.render(<About />, document.getElementById('about-root'));