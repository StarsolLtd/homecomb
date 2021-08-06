import React from 'react';
import {Breadcrumb, BreadcrumbItem, Col, Container, Row} from 'reactstrap';
import Constants from "../Constants";
import {Link} from "react-router-dom";
import ContactForm from "../components/ContactForm";

class Contact extends React.Component {

    componentDidMount() {
        document.title = Constants.SITE_NAME + ' | Contact Us';
    }

    render() {
        return (
            <Container>
                <Row>
                    <Breadcrumb className="w-100">
                        <BreadcrumbItem><Link to="/">{Constants.SITE_NAME}</Link></BreadcrumbItem>
                        <BreadcrumbItem className="active">Contact us</BreadcrumbItem>
                    </Breadcrumb>
                </Row>
                <Row>
                    <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                        <ContactForm />
                    </Col>
                </Row>
            </Container>
        );
    }
}

export default Contact;