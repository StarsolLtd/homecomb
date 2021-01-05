import React from 'react';
import {Container, Col, Row} from 'reactstrap';
import Constants from "../Constants";
import RegisterForm from "../components/RegisterForm";

class Register extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            firstName: '',
            lastName: '',
            plainPassword: '',
            agreeTerms: false,
        };
    }

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Register';
    }

    render() {
        return (
            <Container>
                <Row>
                    <Col md="12" className="page-title">
                        <h1>Register with {Constants.SITE_NAME}</h1>
                    </Col>
                </Row>

                <Row>
                    <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                        <p>
                            To create an account with us, please complete the form below:
                        </p>

                        <hr />

                        <RegisterForm {...this.props} />
                    </Col>
                </Row>
            </Container>
        );
    }
}

export default Register;